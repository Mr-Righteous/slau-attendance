<?php

namespace App\Livewire\Lecturer;

use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\CourseUnit;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ViewCourseAttendance extends Component
{
    public $courses;
    public $selectedCourseId;

    public $sessions = [];
    public $students = [];
    public $attendanceData = [];

    public function mount()
    {
        $this->courses = Auth::user()->coursesTeaching()->get();
    }

    public function updatedSelectedCourseId($courseId)
    {
        if (empty($courseId)) {
            $this->resetData();
            return;
        }

        // Get sessions for the selected course
        $this->sessions = ClassSession::where('course_id', $courseId)
            ->orderBy('date')
            ->get();

        // Get students enrolled in the course
        $this->students = Student::whereHas('enrollments', function ($query) use ($courseId) {
            $query->where('course_id', $courseId);
        })
        ->orderBy('name')
        ->get();

        // Get all attendance records for these students and sessions
        $studentIds = $this->students->pluck('user_id');
        $sessionIds = $this->sessions->pluck('id');

        $records = AttendanceRecord::whereIn('class_session_id', $sessionIds)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy(function ($item) {
                return $item['student_id'] . '-' . $item['class_session_id'];
            });

        // Structure data for easy lookup in the view
        $this->attendanceData = $records;
    }

    public function resetData()
    {
        $this->sessions = [];
        $this->students = [];
        $this->attendanceData = [];
    }

    public function render()
    {
        return view('livewire.lecturer.view-course-attendance');
    }
}