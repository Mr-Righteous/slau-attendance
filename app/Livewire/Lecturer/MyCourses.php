<?php

namespace App\Livewire\Lecturer;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyCourses extends Component
{
    public $courses;

    public function mount()
    {
        $this->courses = Auth::user()->coursesTeaching()
            ->with('department')
            ->withCount('enrollments')
            ->get();
    }

    public function render()
    {
        return view('livewire.lecturer.my-courses');
    }
}