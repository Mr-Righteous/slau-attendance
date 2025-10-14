<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\CourseUnit;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MarkClassAttendance extends Component
{
    // Step 1: Select Course
    public $selectedCourseId;
    public $selectedCourse;
    
    // Step 2: Select Course Unit
    public $courseUnits;
    public $selectedCourseUnitId;
    public $selectedCourseUnit;
    
    // Step 3: Select Lecturer & Create Session
    public $lecturers;
    public $selectedLecturerId;
    
    // Session details
    public $sessionWeek;
    public $sessionDate;
    public $sessionStartTime;
    public $sessionEndTime;
    public $sessionTopic;
    public $sessionVenue;
    public $createdSessionId;
    
    // Step 4: Mark Attendance
    public $students;
    public $manualStudents; // Students added manually
    public $attendance = [];
    
    // UI State
    public $step = 1;
    public $searchStudent = '';
    
    // Add Students Modal State
    public $showAddStudentsModal = false;
    public $modalSearchStudent = '';
    public $availableStudents;
    public $selectedStudentsToAdd = [];
    public $modalFilters = [
        'year' => '',
        'semester' => '',
        'show_all_years' => false,
    ];

    public function mount()
    {
        $this->sessionDate = now()->format('Y-m-d');
        $this->sessionStartTime = '08:00';
        $this->sessionEndTime = '10:00';
    }

    // Step 1: Select Course
    public function selectCourse($courseId)
    {
        $this->selectedCourseId = $courseId;
        $this->selectedCourse = Course::with('department')->find($courseId);
        
        $this->courseUnits = $this->selectedCourse->courseUnits()
            ->with(['department', 'lecturer'])
            ->orderBy('code')
            ->get();
        
        $this->step = 2;
    }

    // Step 2: Select Course Unit
    public function selectCourseUnit($courseUnitId)
    {
        $this->selectedCourseUnitId = $courseUnitId;
        $this->selectedCourseUnit = CourseUnit::with(['department', 'lecturer'])->find($courseUnitId);
        
        $this->lecturers = User::forUserRole(Auth::user())->whereHas('roles', function ($query) {
            $query->where('name', 'lecturer');
        })->orderBy('name')->get();
        
        $this->selectedLecturerId = $this->selectedCourseUnit->lecturer_id;
        
        $this->step = 3;
    }

    // Step 3: Create Session
    public function createSessionAndContinue()
    {
        $this->validate([
            'selectedLecturerId' => 'required|exists:users,id',
            'sessionWeek' => 'required|integer',
            'sessionDate' => 'required|date',
            'sessionStartTime' => 'required',
            'sessionEndTime' => 'required|after:sessionStartTime',
            'sessionTopic' => 'nullable|string|max:255',
            'sessionVenue' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $session = ClassSession::create([
                'course_unit_id' => $this->selectedCourseUnitId,
                'lecturer_id' => $this->selectedLecturerId,
                'week' => $this->sessionWeek,
                'date' => $this->sessionDate,
                'start_time' => $this->sessionStartTime,
                'end_time' => $this->sessionEndTime,
                'topic' => $this->sessionTopic,
                'venue' => $this->sessionVenue,
            ]);

            $this->createdSessionId = $session->id;
            $this->loadStudentsForSession();
            
            DB::commit();

            Notification::make()
                ->title('Session created successfully')
                ->success()
                ->send();

            $this->step = 4;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error creating session')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // Step 4: Load students
    public function loadStudentsForSession()
    {
        $courseCourseUnit = DB::table('course_course_units')
            ->where('course_id', $this->selectedCourseId)
            ->where('course_unit_id', $this->selectedCourseUnitId)
            ->first();

        if (!$courseCourseUnit) {
            $this->students = collect([]);
            $this->manualStudents = collect([]);
            return;
        }

        // Get currently marked student IDs to track manual additions
        $markedStudentIds = AttendanceRecord::where('class_session_id', $this->createdSessionId)
            ->pluck('student_id')
            ->toArray();

        // Get students in this course who are in the correct year/semester
        $regularStudents = Student::where('course_id', $this->selectedCourseId)
            ->where('current_year', $courseCourseUnit->default_year)
            ->where('current_semester', $courseCourseUnit->default_semester)
            ->with('department')
            ->when($this->searchStudent, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchStudent . '%')
                      ->orWhere('registration_number', 'like', '%' . $this->searchStudent . '%');
                });
            })
            ->orderBy('name')
            ->get();

        $regularStudentIds = $regularStudents->pluck('user_id')->toArray();

        // Find manually added students (those with attendance records but not in regular list)
        $manualStudentIds = array_diff($markedStudentIds, $regularStudentIds);
        
        $this->manualStudents = collect([]);
        if (!empty($manualStudentIds)) {
            $this->manualStudents = Student::whereIn('user_id', $manualStudentIds)
                ->with('department')
                ->orderBy('name')
                ->get();
        }

        // Combine regular and manual students
        $this->students = $regularStudents->merge($this->manualStudents);

        // Load existing attendance
        $existingAttendance = AttendanceRecord::where('class_session_id', $this->createdSessionId)
            ->pluck('status', 'student_id')
            ->toArray();

        // Initialize attendance array
        foreach ($this->students as $student) {
            $this->attendance[$student->user_id] = $existingAttendance[$student->user_id] ?? 'absent';
        }
    }

    // Remove manually added student
    public function removeManualStudent($studentId)
    {
        DB::beginTransaction();

        try {
            // Delete the attendance record
            AttendanceRecord::where('class_session_id', $this->createdSessionId)
                ->where('student_id', $studentId)
                ->delete();

            // Remove from attendance array
            unset($this->attendance[$studentId]);

            // Remove the student from the list of manualStudents
            $this->manualStudents = $this->manualStudents->reject(function ($student) use ($studentId) {
                return $student->user_id == $studentId; // Fixed: use user_id instead of id
            });

            // Also remove from the main students list
            $this->students = $this->students->reject(function ($student) use ($studentId) {
                return $student->user_id == $studentId;
            });

            DB::commit();

            Notification::make()
                ->title('Student removed')
                ->success()
                ->send();

            // DON'T call loadStudentsForSession() here as it will reload everything

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error removing student')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // ==========================================
    // ADD STUDENTS MODAL METHODS
    // ==========================================
    
    public function openAddStudentsModal()
    {
        $this->showAddStudentsModal = true;
        $this->selectedStudentsToAdd = [];
        $this->modalSearchStudent = '';
        $this->loadAvailableStudents();
    }

    public function closeAddStudentsModal()
    {
        $this->showAddStudentsModal = false;
        $this->reset(['selectedStudentsToAdd', 'modalSearchStudent', 'availableStudents', 'modalFilters']);
    }

    public function loadAvailableStudents()
    {
        if (!$this->createdSessionId) {
            return;
        }

        // Get student IDs already in the session
        $currentStudentIds = $this->students->pluck('user_id')->toArray();

        // Build query for available students
        $query = Student::where('course_id', $this->selectedCourseId)
            ->whereNotIn('user_id', $currentStudentIds)
            ->with('department');

        // Apply filters
        if (!$this->modalFilters['show_all_years']) {
            // Get default year/semester from pivot
            $courseCourseUnit = DB::table('course_course_units')
                ->where('course_id', $this->selectedCourseId)
                ->where('course_unit_id', $this->selectedCourseUnitId)
                ->first();

            if ($courseCourseUnit) {
                $query->where('current_year', $courseCourseUnit->default_year)
                      ->where('current_semester', $courseCourseUnit->default_semester);
            }
        } else {
            // Show all students from this course
            if ($this->modalFilters['year']) {
                $query->where('current_year', $this->modalFilters['year']);
            }
            if ($this->modalFilters['semester']) {
                $query->where('current_semester', $this->modalFilters['semester']);
            }
        }

        // Apply search
        if ($this->modalSearchStudent) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->modalSearchStudent . '%')
                  ->orWhere('registration_number', 'like', '%' . $this->modalSearchStudent . '%');
            });
        }

        $this->availableStudents = $query->orderBy('name')->get();
    }

    public function updatedModalSearchStudent()
    {
        $this->loadAvailableStudents();
    }

    public function updatedModalFilters()
    {
        $this->loadAvailableStudents();
    }

    public function toggleStudentSelection($studentId)
    {
        if (in_array($studentId, $this->selectedStudentsToAdd)) {
            $this->selectedStudentsToAdd = array_diff($this->selectedStudentsToAdd, [$studentId]);
        } else {
            $this->selectedStudentsToAdd[] = $studentId;
        }
    }

    public function selectAllAvailableStudents()
    {
        $this->selectedStudentsToAdd = $this->availableStudents->pluck('user_id')->toArray();
    }

    public function deselectAllAvailableStudents()
    {
        $this->selectedStudentsToAdd = [];
    }

    public function addSelectedStudents()
    {
        if (empty($this->selectedStudentsToAdd)) {
            Notification::make()
                ->title('No students selected')
                ->warning()
                ->send();
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($this->selectedStudentsToAdd as $studentId) {
                // Create attendance record with default 'absent' status
                AttendanceRecord::firstOrCreate(
                    [
                        'class_session_id' => $this->createdSessionId,
                        'student_id' => $studentId,
                    ],
                    [
                        'status' => 'absent',
                        'marked_by' => Auth::user()->id,
                        'marked_at' => now(),
                    ]
                );
            }

            DB::commit();

            $count = count($this->selectedStudentsToAdd);
            
            Notification::make()
                ->title('Students added successfully')
                ->body("$count student(s) added to session")
                ->success()
                ->send();

            // Reload the students list
            $this->loadStudentsForSession();
            $this->closeAddStudentsModal();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error adding students')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // ==========================================
    // ATTENDANCE MARKING METHODS
    // ==========================================

    public function markAll($status)
    {
        foreach ($this->students as $student) {
            $this->attendance[$student->user_id] = $status;
        }
    }

    public function saveAttendance()
    {
        if (empty($this->attendance)) {
            Notification::make()
                ->title('No students to mark')
                ->warning()
                ->send();
            return;
        }

        DB::beginTransaction();

        try {
            $marked = 0;
            
            foreach ($this->attendance as $studentId => $status) {
                AttendanceRecord::updateOrCreate(
                    [
                        'class_session_id' => $this->createdSessionId,
                        'student_id' => $studentId,
                    ],
                    [
                        'status' => $status,
                        'marked_by' => Auth::user()->id,
                        'marked_at' => now(),
                    ]
                );
                $marked++;
            }

            DB::commit();

            Notification::make()
                ->title('Attendance saved successfully')
                ->body("$marked students marked for " . $this->selectedCourseUnit->code)
                ->success()
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error saving attendance')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // Navigation
    public function goToStep($step)
    {
        $this->step = $step;
    }

    public function resetComponent()
    {
        $this->reset();
        $this->mount();
    }

    public function render()
    {
        $courses = Course::forUserRole(Auth::user())->with('department')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.mark-class-attendance', [
            'courses' => $courses,
        ]);
    }
}