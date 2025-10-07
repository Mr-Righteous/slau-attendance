<?php

namespace App\Livewire\Admin;

use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Student;
use Livewire\Component;

class AddStudentsModal extends Component
{
    public $isOpen = false;
    public $sessionId;
    public $selectedStudents = [];
    public $filters = [
        'course_id' => '',
        'academic_year' => '',
        'year_of_study' => '',
        'semester' => ''
    ];

    public $eligibleStudents = [];
    public $courses = [];
    public $academicYears = [];

    protected $listeners = ['openAddStudentsModal' => 'openModal'];

    public function mount()
    {
        $this->courses = Course::orderBy('code')->get();
        $this->academicYears = $this->getAcademicYears();
        
        // Set default filters to current academic year/semester
        $this->filters['academic_year'] = now()->year;
        $currentMonth = now()->month;
        $this->filters['semester'] = $currentMonth >= 8 || $currentMonth <= 1 ? 1 : 2;
    }

    public function openModal($sessionId = null)
    {
        $this->sessionId = $sessionId;
        $this->isOpen = true;
        $this->reset(['selectedStudents', 'eligibleStudents']);
        
        // Load eligible students when modal opens
        $this->loadEligibleStudents();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['selectedStudents', 'eligibleStudents', 'filters']);
    }

    public function loadEligibleStudents()
    {
        if (!$this->sessionId) {
            return;
        }

        $session = ClassSession::find($this->sessionId);
        
        $this->eligibleStudents = Student::getEligibleStudentsForSession($session, $this->filters);
    }

    public function addStudentsToSession()
    {
        if (empty($this->selectedStudents) || !$this->sessionId) {
            return;
        }

        // Here you would add the logic to associate students with the session
        // This could be through a pivot table or by creating attendance records
        
        $this->emit('studentsAdded', $this->selectedStudents);
        $this->closeModal();
        
        // Show success message
        session()->flash('success', count($this->selectedStudents) . ' students added to session successfully.');
    }

    private function getAcademicYears()
    {
        $currentYear = now()->year;
        return range($currentYear - 2, $currentYear + 1);
    }

    public function render()
    {
        return view('livewire.admin.add-students-modal');
    }
}