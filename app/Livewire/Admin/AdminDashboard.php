<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $dateRange = 'today'; // today, week, month, all

    public function render()
    {
        // Count statistics
        $stats = [
            'total_students' => User::whereHas('roles', function ($q) {
                $q->where('name', 'student');
            })->count(),
            'total_lecturers' => User::whereHas('roles', function ($q) {
                $q->where('name', 'lecturer');
            })->count(),
            'total_courses' => Course::count(),
            'total_course_units' => CourseUnit::count(),
            'total_sessions' => ClassSession::count(),
            'total_attendance_records' => AttendanceRecord::count(),
        ];

        if (Auth::user()->hasRole('dpt-hod')) {

            $departmentId = Auth::user()->department_id;

            $stats = [
                'total_students' => User::where('department_id', $departmentId)->whereHas('roles', function ($q) {
                    $q->where('name', 'student');
                })->count(),
                'total_lecturers' => User::where('department_id', $departmentId)->whereHas('roles', function ($q) {
                    $q->where('name', 'lecturer');
                })->count(),
                'total_courses' => Course::where('department_id', $departmentId)->count(),
                'total_course_units' => CourseUnit::where('department_id', $departmentId)->count(),
                'total_sessions' => ClassSession::whereHas('courseUnit', function ($query) use ($departmentId) 
                {
                    $query->where('department_id', $departmentId);
                })->count(),
                'total_attendance_records' => AttendanceRecord::whereHas('classSession', function ($query) use ($departmentId)
                {
                    $query->whereHas('courseUnit', function ($courseQuery) use ($departmentId) {
                        $courseQuery->where('department_id', $departmentId);
                    });
                })->count(),
            ];
        }

        // Today's statistics
        $today = now()->toDateString();
        $todayStats = [
            'sessions' => ClassSession::whereDate('date', $today)->count(),
            'attendance_marked' => AttendanceRecord::whereHas('classSession', function ($q) use ($today) {
                $q->whereDate('date', $today);
            })->count(),
        ];

        // Attendance rate calculation based on date range
        $attendanceQuery = AttendanceRecord::query();
        $sessionsQuery = ClassSession::query();

        switch ($this->dateRange) {
            case 'today':
                $attendanceQuery->whereHas('classSession', function ($q) {
                    $q->whereDate('date', now()->toDateString());
                });
                $sessionsQuery->whereDate('date', now()->toDateString());
                break;
            case 'week':
                $attendanceQuery->whereHas('classSession', function ($q) {
                    $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                });
                $sessionsQuery->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $attendanceQuery->whereHas('classSession', function ($q) {
                    $q->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                });
                $sessionsQuery->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
        }

        $totalAttendance = $attendanceQuery->count();
        $presentCount = (clone $attendanceQuery)->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : 0;

        if (Auth::user()->hasRole('dpt-hod')) {

            $department = Auth::user()->department;
            $departmentId = Auth::user()->department_id;

                // Today's statistics
            $today = now()->toDateString();
            $todayStats = [
                'sessions' => $department->classSessions()->whereDate('date', $today)->count(),
                'attendance_marked' => $department->attendanceRecords()->whereHas('classSession', function ($q) use ($today) {
                    $q->whereDate('date', $today);
                })->count(),
            ];

            // Attendance rate calculation based on date range
            $attendanceQuery = $department->attendanceRecords();
            $sessionsQuery = $department->classSessions();

            switch ($this->dateRange) {
                case 'today':
                    $attendanceQuery->whereHas('classSession', function ($q) {
                        $q->whereDate('date', now()->toDateString());
                    });
                    $sessionsQuery->whereDate('date', now()->toDateString());
                    break;
                case 'week':
                    $attendanceQuery->whereHas('classSession', function ($q) {
                        $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                    });
                    $sessionsQuery->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $attendanceQuery->whereHas('classSession', function ($q) {
                        $q->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                    });
                    $sessionsQuery->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
            }

            $totalAttendance = $attendanceQuery->count();
            $presentCount = (clone $attendanceQuery)->whereIn('status', ['present', 'late'])->count();
            $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : 0;

        }


        // Recent activity - using course units instead of courses
        $recentSessions = ClassSession::with(['courseUnit.courses'])
            ->withCount('attendanceRecords')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Low attendance alerts (sessions with < 60% attendance)
        $lowAttendanceSessions = ClassSession::with(['courseUnit.courses'])
            ->withCount([
                'attendanceRecords',
                'attendanceRecords as present_count' => function ($q) {
                    $q->whereIn('status', ['present', 'late']);
                }
            ])
            ->whereHas('attendanceRecords')
            ->get()
            ->filter(function ($session) {
                $total = $session->attendance_records_count;
                $present = $session->present_count;
                $rate = $total > 0 ? ($present / $total) * 100 : 0;
                return $rate < 60;
            })
            ->take(5);

        // Students with low attendance (< 75%)
        $lowAttendanceStudents = Student::with(['user', 'course', 'department'])
            ->get()
            ->map(function ($student) {
                // Get attendance for student's current course units
                $courseUnits = $student->getDefaultCourseUnits();
                $totalSessions = 0;
                $attendedSessions = 0;

                foreach ($courseUnits as $courseUnit) {
                    $totalSessions += ClassSession::where('course_unit_id', $courseUnit->id)->count();
                    $attendedSessions += AttendanceRecord::where('student_id', $student->user_id)
                        ->whereHas('classSession', function ($q) use ($courseUnit) {
                            $q->where('course_unit_id', $courseUnit->id);
                        })
                        ->whereIn('status', ['present', 'late'])
                        ->count();
                }

                $student->total_sessions = $totalSessions;
                $student->attended_sessions = $attendedSessions;
                $student->attendance_rate = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 2) : 0;
                
                return $student;
            })
            ->filter(function ($student) {
                return $student->total_sessions > 0 && $student->attendance_rate < 75;
            })
            ->sortBy('attendance_rate')
            ->take(10);

        // Courses by student count
        $popularCourses = Course::withCount('students')
            ->orderBy('students_count', 'desc')
            ->limit(5)
            ->get();

        // Recent students
        $recentStudents = Student::with(['course', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if (Auth::user()->hasRole('dpt-hod')) {

            $department = Auth::user()->department;
            $departmentId = Auth::user()->department_id;

            // Recent activity - using course units instead of courses
            $recentSessions = $department->classSessions()->with(['courseUnit.courses'])
            ->withCount('attendanceRecords')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

            // Low attendance alerts (sessions with < 60% attendance)
            $lowAttendanceSessions = $department->classSessions()->with(['courseUnit.courses'])
                ->withCount([
                    'attendanceRecords',
                    'attendanceRecords as present_count' => function ($q) {
                        $q->whereIn('status', ['present', 'late']);
                    }
                ])
                ->whereHas('attendanceRecords')
                ->get()
                ->filter(function ($session) {
                    $total = $session->attendance_records_count;
                    $present = $session->present_count;
                    $rate = $total > 0 ? ($present / $total) * 100 : 0;
                    return $rate < 60;
                })
                ->take(5);

            // Students with low attendance (< 75%)
            $lowAttendanceStudents = Student::where('department_id', $departmentId)->with(['user', 'course', 'department'])
                ->get()
                ->map(function ($student) {
                    // Get attendance for student's current course units
                    $courseUnits = $student->getDefaultCourseUnits();
                    $totalSessions = 0;
                    $attendedSessions = 0;

                    foreach ($courseUnits as $courseUnit) {
                        $totalSessions += ClassSession::where('course_unit_id', $courseUnit->id)->count();
                        $attendedSessions += AttendanceRecord::where('student_id', $student->user_id)
                            ->whereHas('classSession', function ($q) use ($courseUnit) {
                                $q->where('course_unit_id', $courseUnit->id);
                            })
                            ->whereIn('status', ['present', 'late'])
                            ->count();
                    }

                    $student->total_sessions = $totalSessions;
                    $student->attended_sessions = $attendedSessions;
                    $student->attendance_rate = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 2) : 0;
                    
                    return $student;
                })
                ->filter(function ($student) {
                    return $student->total_sessions > 0 && $student->attendance_rate < 75;
                })
                ->sortBy('attendance_rate')
                ->take(10);

            // Courses by student count
            $popularCourses = Course::where('department_id', $departmentId)->withCount('students')
                ->orderBy('students_count', 'desc')
                ->limit(5)
                ->get();

            // Recent students
            $recentStudents = Student::where('department_id', $departmentId)->with(['course', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'todayStats' => $todayStats,  
            'attendanceRate' => $attendanceRate,
            'recentSessions' => $recentSessions,
            'lowAttendanceSessions' => $lowAttendanceSessions,
            'lowAttendanceStudents' => $lowAttendanceStudents,
            'popularCourses' => $popularCourses,
            'recentStudents' => $recentStudents,
        ]);
    }
}