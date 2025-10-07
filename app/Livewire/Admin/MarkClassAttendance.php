<?php

// ============================================
// COMPONENT: MarkClassAttendance.php (UPDATED)
// app/Livewire/Admin/MarkClassAttendance.php
// ============================================

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
    public $courseUnits = [];
    public $selectedCourseUnitId;
    public $selectedCourseUnit;
    
    // Step 3: Select Lecturer & Create Session
    public $lecturers = [];
    public $selectedLecturerId;
    
    // Session details
    public $sessionDate;
    public $sessionStartTime;
    public $sessionEndTime;
    public $sessionTopic;
    public $sessionVenue;
    public $createdSessionId;
    
    // Step 4: Mark Attendance
    public $students = [];
    public $attendance = [];
    
    // UI State
    public $step = 1; // 1=course, 2=course unit, 3=lecturer+session, 4=mark attendance
    public $searchStudent = '';

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
        
        // Load course units for this course
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
        
        // Load lecturers
        $this->lecturers = User::whereHas('roles', function ($query) {
            $query->where('name', 'lecturer');
        })->orderBy('name')->get();
        
        // Pre-select default lecturer if available
        $this->selectedLecturerId = $this->selectedCourseUnit->lecturer_id;
        
        $this->step = 3;
    }

    // Step 3: Create Session with Lecturer
    public function createSessionAndContinue()
    {
        $this->validate([
            'selectedLecturerId' => 'required|exists:users,id',
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
                'date' => $this->sessionDate,
                'start_time' => $this->sessionStartTime,
                'end_time' => $this->sessionEndTime,
                'topic' => $this->sessionTopic,
                'venue' => $this->sessionVenue,
            ]);

            $this->createdSessionId = $session->id;
            
            // Load students for this session
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
        // Get the pivot data to know which year/semester this course unit belongs to for this course
        $courseCourseUnit = DB::table('course_course_units')
            ->where('course_id', $this->selectedCourseId)
            ->where('course_unit_id', $this->selectedCourseUnitId)
            ->first();

        if (!$courseCourseUnit) {
            $this->students = collect([]);
            return;
        }

        // Get students in this course who are in the correct year/semester for this course unit
        $this->students = Student::where('course_id', $this->selectedCourseId)
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

        // TODO: Add students from course_unit_exceptions table (retakes)
        // This will be added later

        // Load existing attendance
        $existingAttendance = AttendanceRecord::where('class_session_id', $this->createdSessionId)
            ->pluck('status', 'student_id')
            ->toArray();

        // Initialize attendance array
        foreach ($this->students as $student) {
            $this->attendance[$student->user_id] = $existingAttendance[$student->user_id] ?? 'absent';
        }
    }

    // Quick actions
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
        $courses = Course::with('department')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.mark-class-attendance', [
            'courses' => $courses,
        ]);
    }
}