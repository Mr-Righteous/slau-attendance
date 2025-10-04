<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseUnit;
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
    
    // Step 2: Select/Create Session
    public $sessions = [];
    public $selectedSessionId;
    public $selectedSession;
    
    // Create new session fields
    public $showCreateSession = false;
    public $sessionDate;
    public $sessionStartTime;
    public $sessionEndTime;
    public $sessionTopic;
    public $sessionVenue;
    public $sessionLecturerId; // Who's teaching this specific session
    
    // Step 3: Mark Attendance
    public $students = [];
    public $attendance = []; // student_id => status
    
    // UI State
    public $step = 1; // 1=select course, 2=select session, 3=mark attendance
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
        $this->selectedCourse = CourseUnit::with(['lecturer', 'department'])->find($courseId);
        $this->sessionLecturerId = $this->selectedCourse->lecturer_id;
        
        // Load sessions for this course
        $this->sessions = ClassSession::where('course_id', $courseId)
            ->with('lecturer')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
        
        $this->step = 2;
    }

    // Step 2: Select existing session or create new
    public function selectSession($sessionId)
    {
        $this->selectedSessionId = $sessionId;
        $this->selectedSession = ClassSession::with('course', 'lecturer')->find($sessionId);
        $this->loadStudentsForSession();
        $this->step = 3;
    }

    public function toggleCreateSession()
    {
        $this->showCreateSession = !$this->showCreateSession;
    }

    public function createSession()
    {
        $this->validate([
            'sessionDate' => 'required|date',
            'sessionStartTime' => 'required',
            'sessionEndTime' => 'required|after:sessionStartTime',
            'sessionLecturerId' => 'required|exists:users,id',
            'sessionTopic' => 'nullable|string|max:255',
            'sessionVenue' => 'nullable|string|max:255',
        ]);

        $session = ClassSession::create([
            'course_id' => $this->selectedCourseId,
            'lecturer_id' => $this->sessionLecturerId,
            'date' => $this->sessionDate,
            'start_time' => $this->sessionStartTime,
            'end_time' => $this->sessionEndTime,
            'topic' => $this->sessionTopic,
            'venue' => $this->sessionVenue,
        ]);

        $this->sessions = ClassSession::where('course_id', $this->selectedCourseId)
            ->orderBy('date', 'desc')
            ->get();

        $this->showCreateSession = false;
        $this->selectSession($session->id);

        Notification::make()
            ->title('Session created successfully')
            ->success()
            ->send();
    }

    // Step 3: Load students and mark attendance
    public function loadStudentsForSession()
    {
        // Get all students enrolled in this course
        $this->students = Student::whereHas('enrollments', function ($query) {
                $query->where('course_id', $this->selectedCourseId);
            })
            ->with('department')
            ->when($this->searchStudent, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchStudent . '%')
                      ->orWhere('registration_number', 'like', '%' . $this->searchStudent . '%');
                });
            })
            ->orderBy('name')
            ->get();

        // Load existing attendance for this session
        $existingAttendance = AttendanceRecord::where('class_session_id', $this->selectedSessionId)
            ->pluck('status', 'student_id')
            ->toArray();

        // Initialize attendance array with existing or default to absent
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
                        'class_session_id' => $this->selectedSessionId,
                        'student_id' => $studentId,
                    ],
                    [
                        'status' => $status,
                        'marked_by' => Auth::user()->id(),
                        'marked_at' => now(),
                    ]
                );
                $marked++;
            }

            DB::commit();

            Notification::make()
                ->title('Attendance saved successfully')
                ->body("$marked students marked for " . $this->selectedSession->course->code)
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
        $courses = CourseUnit::with(['lecturer', 'department'])
            ->withCount('enrollments')
            ->orderBy('code')
            ->get();

        $lecturers = User::whereHas('roles', function ($query) {
            $query->where('name', 'lecturer');
        })->orderBy('name')->get();

        return view('livewire.admin.mark-class-attendance', [
            'courses' => $courses,
            'lecturers' => $lecturers,
        ]);
    }
}