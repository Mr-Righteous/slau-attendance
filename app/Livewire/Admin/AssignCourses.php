<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Student;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class AssignCourses extends Component
{
    use WithPagination;

    public $showAssignModal = false;
    public $selectedStudent;
    public $selectedCourseId;
    public $searchStudent = '';

    // Filters
    public $filterCourse = '';
    public $search = '';

    public function openAssignModal($studentId)
    {
        $this->selectedStudent = Student::with('user', 'course')->find($studentId);
        $this->selectedCourseId = $this->selectedStudent->course_id;
        $this->showAssignModal = true;
    }

    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->reset(['selectedStudent', 'selectedCourseId']);
    }

    public function assignCourse()
    {
        $this->validate([
            'selectedCourseId' => 'required|exists:courses,id',
        ]);

        if ($this->selectedStudent) {
            $this->selectedStudent->update([
                'course_id' => $this->selectedCourseId,
            ]);

            Notification::make()
                ->title('Program assigned successfully')
                ->success()
                ->send();

            $this->closeAssignModal();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCourse()
    {
        $this->resetPage();
    }

    public function render()
    {
        $students = Student::with(['user', 'course', 'department'])
            ->when($this->filterCourse, function ($query) {
                $query->where('course_id', $this->filterCourse);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('registration_number', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(20);

        $courses = Course::orderBy('name')->get();

        return view('livewire.admin.assign-courses', [
            'students' => $students,
            'courses' => $courses,
        ]);
    }
}
