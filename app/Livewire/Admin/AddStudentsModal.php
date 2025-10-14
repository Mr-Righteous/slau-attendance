<?php

namespace App\Livewire\Admin;

use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Student;
use App\Models\CourseUnit;
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

    public function updatedFilters()
    {
        $this->loadEligibleStudents();
    }

    public function loadEligibleStudents()
    {
        if (!$this->sessionId) {
            return;
        }

        $session = ClassSession::with('courseUnit')->find($this->sessionId);
        
        if (!$session) {
            return;
        }

        // Get students who are eligible for this course unit but might not be in the default year/semester
        $query = Student::with(['course', 'department', 'user'])
            ->whereHas('course.courseUnits', function ($q) use ($session) {
                $q->where('course_units.id', $session->course_unit_id);
            });

        // Apply filters
        if ($this->filters['course_id']) {
            $query->where('course_id', $this->filters['course_id']);
        }

        if ($this->filters['academic_year']) {
            $query->where('academic_year', $this->filters['academic_year']);
        }

        if ($this->filters['year_of_study']) {
            $query->where('current_year', $this->filters['year_of_study']);
        }

        if ($this->filters['semester']) {
            $query->where('current_semester', $this->filters['semester']);
        }

        $this->eligibleStudents = $query->orderBy('name')->get();
    }

    public function addStudentsToSession()
    {
        if (empty($this->selectedStudents) || !$this->sessionId) {
            session()->flash('error', 'Please select at least one student.');
            return;
        }

        // Emit event to parent component with selected students
        $this->dispatch('studentsAdded', 
            sessionId: $this->sessionId,
            studentIds: $this->selectedStudents
        );

        $this->closeModal();
        
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