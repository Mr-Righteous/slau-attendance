<?php

namespace App\Livewire\Student;

use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentCourses extends Component
{
    public $enrollments;

    public function mount()
    {
        $this->enrollments = Enrollment::where('student_id', Auth::id())
            ->with('courseUnit.lecturer', 'courseUnit.department')
            ->get();
    }

    public function render()
    {
        return view('livewire.student.student-courses');
    }
}