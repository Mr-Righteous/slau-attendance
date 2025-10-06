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
    public $lecturer_id;
    public $department_id;
    public $semester;
    public $academic_year;
    public $credits = 3;
    
    // Filters
    public $search = '';
    public $filterDepartment = '';
    public $filterSemester = '';

    protected $rules = [
        'code' => 'required|string|max:20|unique:courses,code',
        'name' => 'required|string|max:255',
        'lecturer_id' => 'nullable|exists:users,id',
        'department_id' => 'required|exists:departments,id',
        'semester' => 'required|string|max:20',
        'academic_year' => 'required|string|max:20',
        'credits' => 'required|integer|min:1|max:10',
    ];

    protected $queryString = ['search', 'filterDepartment', 'filterSemester'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
        $this->academic_year = date('Y') . '/' . (date('Y') + 1);
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $course = Course::findOrFail($id);
        
        $this->courseId = $course->id;
        $this->code = $course->code;
        $this->name = $course->name;
        $this->lecturer_id = $course->lecturer_id;
        $this->department_id = $course->department_id;
        $this->semester = $course->semester;
        $this->academic_year = $course->academic_year;
        $this->credits = $course->credits;
        
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
        $this->lecturer_id = null;
        $this->department_id = null;
        $this->semester = '';
        $this->academic_year = '';
        $this->credits = 3;
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
                    'lecturer_id' => $this->lecturer_id,
                    'department_id' => $this->department_id,
                    'semester' => $this->semester,
                    'academic_year' => $this->academic_year,
                    'credits' => $this->credits,
                ]);

                $message = 'Course updated successfully';
            } else {
                Course::create([
                    'code' => $this->code,
                    'name' => $this->name,
                    'lecturer_id' => $this->lecturer_id,
                    'department_id' => $this->department_id,
                    'semester' => $this->semester,
                    'academic_year' => $this->academic_year,
                    'credits' => $this->credits,
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
            ->with(['lecturer', 'department'])
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
            ->when($this->filterSemester, function ($query) {
                $query->where('semester', $this->filterSemester);
            })
            ->orderBy('code')
            ->paginate(15);

        $lecturers = User::whereHas('roles', function ($query) {
            $query->where('name', 'lecturer');
        })->orderBy('name')->get();

        $departments = Department::orderBy('name')->get();

        return view('livewire.admin.manage-courses', [
            'courses' => $courses,
            'lecturers' => $lecturers,
            'departments' => $departments,
        ]);
    }
}