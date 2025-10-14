<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Department;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdvancedReports extends Component
{
    // Filters
    public $selectedCourseId = ''; // Changed from selectedProgramId
    public $selectedDepartmentId = '';
    public $threshold = 75;

    protected $listeners = [
        'runReport' => 'runReport'
    ];
    // Data
    public $atRiskStudents = [];

    protected $attendanceService;

    public function boot(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function runReport()
    {
        $this->atRiskStudents = $this->attendanceService->getAtRiskStudents(
            $this->threshold,
            $this->selectedCourseId ?: null, // Changed parameter
            $this->selectedDepartmentId ?: null
        );
    }

    // Update your properties to use debounced updates
    public function updated($property)
    {
        if (in_array($property, ['selectedDepartmentId', 'selectedCourseId', 'threshold'])) {
            $this->runReport();
        }
    }

    public function mount()
    {
        $this->runReport();
    }

    public function render()
    {
        $departments = Department::orderBy('name')->get();
        
        // Filter courses based on selected department
        $coursesQuery = Course::forUserRole(Auth::user())->orderBy('name');
        if ($this->selectedDepartmentId) {
            $coursesQuery->where('department_id', $this->selectedDepartmentId);
        }
        $courses = $coursesQuery->get();

        return view('livewire.admin.advanced-reports', [
            'departments' => $departments,
            'courses' => $courses,
        ]);
    }
}