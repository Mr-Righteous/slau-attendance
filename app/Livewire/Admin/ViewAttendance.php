<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ViewAttendance extends Component
{
    use WithPagination;

    // Filters
    public $filterCourse = '';
    public $filterCourseUnit = '';
    public $filterSession = '';
    public $filterStatus = '';
    public $filterDepartment = '';
    public $searchStudent = '';
    public $dateFrom = '';
    public $dateTo = '';

    // View mode
    public $viewMode = 'records'; // records, sessions, students

    // Stats
    public $showStats = true;

    protected $queryString = [
        'filterCourse',
        'filterCourseUnit',
        'filterStatus',
        'searchStudent',
        'viewMode',
    ];

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedFilterCourse($value)
    {
        $this->resetPage();
        $this->filterCourseUnit = '';
        $this->filterSession = '';
    }

    public function updatedFilterCourseUnit($value)
    {
        $this->resetPage();
        $this->filterSession = '';
    }

    public function updatingSearchStudent()
    {
        $this->resetPage();
    }

    public function updatingViewMode()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'filterCourse',
            'filterCourseUnit',
            'filterSession',
            'filterStatus',
            'filterDepartment',
            'searchStudent',
            'dateFrom',
            'dateTo',
        ]);
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function exportData()
    {
        // This would export filtered data to CSV/Excel
        // For now, just show notification
        \Filament\Notifications\Notification::make()
            ->title('Export feature')
            ->body('Export functionality will be implemented in the next phase')
            ->info()
            ->send();
    }

    public function getStatsProperty()
    {
        $query = AttendanceRecord::query()
            ->when($this->dateFrom, function ($q) {
                $q->whereHas('classSession', function ($query) {
                    $query->where('date', '>=', $this->dateFrom);
                });
            })
            ->when($this->dateTo, function ($q) {
                $q->whereHas('classSession', function ($query) {
                    $query->where('date', '<=', $this->dateTo);
                });
            })
            ->when($this->filterCourse, function ($q) {
                $q->whereHas('classSession.courseUnit', function ($query) {
                    $query->whereHas('courses', function ($q) {
                        $q->where('courses.id', $this->filterCourse);
                    });
                });
            })
            ->when($this->filterCourseUnit, function ($q) {
                $q->whereHas('classSession', function ($query) {
                    $query->where('course_unit_id', $this->filterCourseUnit);
                });
            });

        $total = (clone $query)->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();
        $excused = (clone $query)->where('status', 'excused')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'attendance_rate' => $total > 0 ? round((($present + $late) / $total) * 100, 2) : 0,
        ];
    }

    public function render()
    {
        // Get courses for filter
        $courses = Course::orderBy('code')->get();
        $departments = Department::orderBy('name')->get();

        // Get course units for selected course
        $courseUnits = [];
        if ($this->filterCourse) {
            $courseUnits = CourseUnit::whereHas('courses', function ($query) {
                $query->where('courses.id', $this->filterCourse);
            })->orderBy('name')->get();
        }

        // Get sessions for selected course unit
        $sessions = [];
        if ($this->filterCourseUnit) {
            $sessions = ClassSession::where('course_unit_id', $this->filterCourseUnit)
                ->orderBy('date', 'desc')
                ->get();
        }

        $data = null;

        // View Mode: Individual Records
        if ($this->viewMode === 'records') {
            $data = AttendanceRecord::query()
                ->with(['student.department', 'classSession.courseUnit.courses', 'markedBy'])
                ->when($this->filterCourse, function ($query) {
                    $query->whereHas('classSession.courseUnit', function ($q) {
                        $q->whereHas('courses', function ($query) {
                            $query->where('courses.id', $this->filterCourse);
                        });
                    });
                })
                ->when($this->filterCourseUnit, function ($query) {
                    $query->whereHas('classSession', function ($q) {
                        $q->where('course_unit_id', $this->filterCourseUnit);
                    });
                })
                ->when($this->filterSession, function ($query) {
                    $query->where('class_session_id', $this->filterSession);
                })
                ->when($this->filterStatus, function ($query) {
                    $query->where('status', $this->filterStatus);
                })
                ->when($this->filterDepartment, function ($query) {
                    $query->whereHas('student', function ($q) {
                        $q->where('department_id', $this->filterDepartment);
                    });
                })
                ->when($this->searchStudent, function ($query) {
                    $query->whereHas('student', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchStudent . '%')
                          ->orWhere('registration_number', 'like', '%' . $this->searchStudent . '%');
                    });
                })
                ->when($this->dateFrom, function ($query) {
                    $query->whereHas('classSession', function ($q) {
                        $q->where('date', '>=', $this->dateFrom);
                    });
                })
                ->when($this->dateTo, function ($query) {
                    $query->whereHas('classSession', function ($q) {
                        $q->where('date', '<=', $this->dateTo);
                    });
                })
                ->orderBy('marked_at', 'desc')
                ->paginate(20);
        }

        // View Mode: By Session
        if ($this->viewMode === 'sessions') {
            $data = ClassSession::query()
                ->with(['courseUnit.courses.department', 'attendanceRecords'])
                ->withCount([
                    'attendanceRecords',
                    'attendanceRecords as present_count' => function ($query) {
                        $query->where('status', 'present');
                    },
                    'attendanceRecords as late_count' => function ($query) {
                        $query->where('status', 'late');
                    },
                    'attendanceRecords as absent_count' => function ($query) {
                        $query->where('status', 'absent');
                    },
                ])
                ->when($this->filterCourse, function ($query) {
                    $query->whereHas('courseUnit', function ($q) {
                        $q->whereHas('courses', function ($query) {
                            $query->where('courses.id', $this->filterCourse);
                        });
                    });
                })
                ->when($this->filterCourseUnit, function ($query) {
                    $query->where('course_unit_id', $this->filterCourseUnit);
                })
                ->when($this->dateFrom, function ($query) {
                    $query->where('date', '>=', $this->dateFrom);
                })
                ->when($this->dateTo, function ($query) {
                    $query->where('date', '<=', $this->dateTo);
                })
                ->orderBy('date', 'desc')
                ->paginate(15);
        }

        // View Mode: By Student
        if ($this->viewMode === 'students') {
            $studentsQuery = User::query()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'student');
                })
                ->with(['department', 'student.course'])
                ->when($this->searchStudent, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->searchStudent . '%')
                          ->orWhereHas('student', function ($q) {
                              $q->where('registration_number', 'like', '%' . $this->searchStudent . '%');
                          });
                    });
                })
                ->when($this->filterDepartment, function ($query) {
                    $query->where('department_id', $this->filterDepartment);
                });

            if ($this->filterCourse) {
                // Filter students enrolled in specific course
                $studentsQuery->whereHas('student', function ($query) {
                    $query->where('course_id', $this->filterCourse);
                });
            }

            $data = $studentsQuery->paginate(20);

            // Add attendance stats for each student
            foreach ($data as $student) {
                $attendanceQuery = AttendanceRecord::where('student_id', $student->id)
                    ->when($this->filterCourse, function ($query) use ($student) {
                        $query->whereHas('classSession.courseUnit', function ($q) use ($student) {
                            $q->whereHas('courses', function ($query) {
                                $query->where('courses.id', $this->filterCourse);
                            });
                        });
                    })
                    ->when($this->filterCourseUnit, function ($query) {
                        $query->whereHas('classSession', function ($q) {
                            $q->where('course_unit_id', $this->filterCourseUnit);
                        });
                    })
                    ->when($this->dateFrom, function ($query) {
                        $query->whereHas('classSession', function ($q) {
                            $q->where('date', '>=', $this->dateFrom);
                        });
                    })
                    ->when($this->dateTo, function ($query) {
                        $query->whereHas('classSession', function ($q) {
                            $q->where('date', '<=', $this->dateTo);
                        });
                    });

                $total = (clone $attendanceQuery)->count();
                $present = (clone $attendanceQuery)->whereIn('status', ['present', 'late'])->count();

                $student->total_sessions = $total;
                $student->attended_sessions = $present;
                $student->attendance_rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;
            }
        }

        return view('livewire.admin.view-attendance', [
            'data' => $data,
            'courses' => $courses,
            'courseUnits' => $courseUnits,
            'sessions' => $sessions,
            'departments' => $departments,
            'stats' => $this->stats,
        ]);
    }
}