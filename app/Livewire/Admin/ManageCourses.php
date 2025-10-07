<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ManageCourses extends Component
{
    use WithPagination;

    public $showModal;
    public $editMode = false;
    public $courseId;
    
    // Form fields
    public $code;
    public $name;
    public $department_id;
    public $duration_years;
    public $description;
    
    // Filters
    public $search = '';
    public $filterDepartment = '';

    protected $rules = [
        'code' => 'required|string|max:20|unique:courses,code',
        'name' => 'required|string|max:255',
        'department_id' => 'required|exists:departments,id',
        'duration_years' => 'required|integer|min:1|max:4',
        'description' => 'nullable|string',
    ];

    protected $queryString = ['search', 'filterDepartment'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $course = Course::findOrFail($id);
        
        $this->courseId = $course->id;
        $this->code = $course->code;
        $this->name = $course->name;
        $this->department_id = $course->department_id;
        $this->duration_years = $course->duration_years;
        $this->description = $course->description;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->courseId = null;
        $this->code = '';
        $this->name = '';
        $this->department_id = null;
        $this->duration_years = null;
        $this->description = '';
        $this->resetErrorBag();
    }

    public function save()
    {
        if ($this->editMode) {
            $this->rules['code'] = 'required|string|max:20|unique:courses,code,' . $this->courseId;
        }

        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->editMode) {
                $course = Course::findOrFail($this->courseId);
                $course->update([
                    'code' => $this->code,
                    'name' => $this->name,
                    'department_id' => $this->department_id,
                    'duration_years' => $this->duration_years,
                    'description' => $this->description,
                ]);

                $message = 'Course updated successfully';
            } else {
                Course::create([
                    'code' => $this->code,
                    'name' => $this->name,
                    'department_id' => $this->department_id,
                    'duration_years' => $this->duration_years,
                    'description' => $this->description,
                ]);

                $message = 'Course created successfully';
            }

            DB::commit();

            Notification::make()
                ->title($message)
                ->success()
                ->send();

            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error saving course')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function delete($id)
    {
        try {
            $course = Course::findOrFail($id);
            
            // Check if course has students
            if ($course->students()->count() > 0) {
                Notification::make()
                    ->title('Cannot delete course')
                    ->body('This course has enrolled students. Remove students first.')
                    ->warning()
                    ->send();
                return;
            }

            // Check if course has sessions
            if ($course->classSessions()->count() > 0) {
                Notification::make()
                    ->title('Cannot delete course')
                    ->body('This course has class sessions. Delete sessions first.')
                    ->warning()
                    ->send();
                return;
            }

            $course->delete();

            Notification::make()
                ->title('Course deleted successfully')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error deleting course')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        $courses = Course::query()
            ->with(['department'])
            ->withCount('students')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDepartment, function ($query) {
                $query->where('department_id', $this->filterDepartment);
            })
            ->orderBy('code')
            ->paginate(15);

        $departments = Department::orderBy('name')->get();

        return view('livewire.admin.manage-courses', [
            'courses' => $courses,
            'departments' => $departments,
        ]);
    }
}