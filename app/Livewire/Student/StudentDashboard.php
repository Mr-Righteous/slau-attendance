<?php

namespace App\Livewire\Student;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentDashboard extends Component
{
    public $student;

    public function mount()
    {
        $this->student = Student::with('program.department')->where('user_id', Auth::id())->first();
    }

    public function render()
    {
        return view('livewire.student.student-dashboard');
    }
}