<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Department;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageCourseUnits extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingCourseUnit = null;

    // Form fields
    public $code = '';
    public $name = '';
    public $description = '';
    public $department_id = '';
    public $lecturer_id = '';
    public $credits = 3;
    public $semester = 1;

    // Filter fields
    public $search = '';
    public $departmentFilter = '';
    public $semesterFilter = '';
    public $defaultYearFilter = '';

    protected $rules = [
        'code' => 'required|string|max:20|unique:course_units,code',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'department_id' => 'required|exists:departments,id',
        'lecturer_id' => 'nullable|exists:users,id',
        'credits' => 'required|integer|min:1|max:10',
        'semester' => 'required|integer|in:1,2',
    ];

    public function openCreateModal()
    {
        $this->reset(['code', 'name', 'description', 'department_id', 'lecturer_id', 'credits', 'semester']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetErrorBag();
    }

    public function createCourseUnit()
    {
        $this->validate();

        try {
            $courseUnit = CourseUnit::create([
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'description' => $this->description,
                'department_id' => $this->department_id,
                'lecturer_id' => $this->lecturer_id ?: null,
                'credits' => $this->credits,
                'semester' => $this->semester,
            ]);

            Notification::make()
                ->title('Course unit created successfully')
                ->success()
                ->send();

            $this->closeCreateModal();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to create course unit')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function editCourseUnit($courseUnitId)
    {
        $this->editingCourseUnit = CourseUnit::with(['department', 'lecturer', 'courses'])->findOrFail($courseUnitId);
        $this->code = $this->editingCourseUnit->code;
        $this->name = $this->editingCourseUnit->name;
        $this->description = $this->editingCourseUnit->description;
        $this->department_id = $this->editingCourseUnit->department_id;
        $this->lecturer_id = $this->editingCourseUnit->lecturer_id;
        $this->credits = $this->editingCourseUnit->credits;
        $this->semester = $this->editingCourseUnit->semester;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingCourseUnit = null;
        $this->resetErrorBag();
    }

    public function updateCourseUnit()
    {
        $this->validate([
            'code' => 'required|string|max:20|unique:course_units,code,' . $this->editingCourseUnit->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'lecturer_id' => 'nullable|exists:users,id',
            'credits' => 'required|integer|min:1|max:10',
            'semester' => 'required|integer|in:1,2',
        ]);

        try {
            $this->editingCourseUnit->update([
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'description' => $this->description,
                'department_id' => $this->department_id,
                'lecturer_id' => $this->lecturer_id ?: null,
                'credits' => $this->credits,
                'semester' => $this->semester,
            ]);

            Notification::make()
                ->title('Course unit updated successfully')
                ->success()
                ->send();

            $this->closeEditModal();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to update course unit')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteCourseUnit($courseUnitId)
    {
        try {
            $courseUnit = CourseUnit::findOrFail($courseUnitId);
            
            // Check if course unit has related records
            if ($courseUnit->classSessions()->exists() || $courseUnit->courses()->exists()) {
                Notification::make()
                    ->title('Cannot delete course unit')
                    ->body('This course unit has associated class sessions or courses. Please remove them first.')
                    ->warning()
                    ->send();
                return;
            }

            $courseUnit->delete();

            Notification::make()
                ->title('Course unit deleted successfully')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to delete course unit')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function linkToCourse($courseUnitId)
    {
        // This would open another modal for linking to courses
        // For now, just show a notification
        Notification::make()
            ->title('Link Course Unit to Courses')
            ->body('This feature will allow you to link this course unit to multiple courses with year/semester settings.')
            ->info()
            ->send();
    }

    public function render()
    {
        $courseUnits = CourseUnit::with(['department', 'lecturer', 'courses', 'classSessions'])
            ->forUserRole(Auth::user())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->departmentFilter, function ($query) {
                $query->where('department_id', $this->departmentFilter);
            })
            ->when($this->semesterFilter, function ($query) {
                $query->where('semester', $this->semesterFilter);
            })
            ->when($this->defaultYearFilter, function ($query) {
                $query->whereHas('courses', function ($q) {
                    $q->where('default_year', $this->defaultYearFilter);
                });
            })
            ->orderBy('code')
            ->paginate(10);

        $departments = Department::orderBy('name')->get();
        $lecturers = User::forUserRole(Auth::user())->whereHas('roles', function ($q) {
            $q->where('name', 'lecturer');
        })->orderBy('name')->get();
        $courses = Course::forUserRole(Auth::user())->orderBy('name')->get();

        return view('livewire.admin.manage-course-units', [
            'courseUnits' => $courseUnits,
            'departments' => $departments,
            'lecturers' => $lecturers,
            'courses' => $courses,
        ]);
    }
}