<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Models\Program;
use App\Services\AttendanceService;
use Livewire\Component;

class AdvancedReports extends Component
{
    // Filters
    public $selectedProgramId = '';
    public $selectedDepartmentId = '';
    public $threshold = 75;

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
            $this->selectedProgramId ?: null,
            $this->selectedDepartmentId ?: null
        );
    }

    public function mount()
    {
        $this->runReport();
    }

    public function render()
    {
        $departments = Department::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();

        return view('livewire.admin.advanced-reports', [
            'departments' => $departments,
            'programs' => $programs,
        ]);
    }
}