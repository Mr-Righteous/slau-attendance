<?php

namespace App\Livewire\Lecturer;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LecturerDashboard extends Component
{
    public $userName;
    public $courseCount;

    public function mount()
    {
        $user = Auth::user();
        $this->userName = $user->name;
        $this->courseCount = $user->coursesTeaching()->count();
    }

    public function render()
    {
        return view('livewire.lecturer.lecturer-dashboard');
    }
}