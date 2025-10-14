<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageDepartments extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingDepartment = null;

    // Form fields
    public $name = '';
    public $code = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:departments,name',
        'code' => 'required|string|max:10|unique:departments,code',
    ];

    public function openCreateModal()
    {
        $this->reset(['name', 'code']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['name', 'code']);
        $this->resetErrorBag();
    }

    public function createDepartment()
    {
        $this->validate();

        try {
            Department::create([
                'name' => $this->name,
                'code' => strtoupper($this->code),
                'faculty_id' => Auth::user()->department->faculty->id,
            ]);

            Notification::make()
                ->title('Department created successfully')
                ->success()
                ->send();

            $this->closeCreateModal();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to create department')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function editDepartment($departmentId)
    {
        $this->editingDepartment = Department::findOrFail($departmentId);
        $this->name = $this->editingDepartment->name;
        $this->code = $this->editingDepartment->code;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingDepartment = null;
        $this->reset(['name', 'code']);
        $this->resetErrorBag();
    }

    public function updateDepartment()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $this->editingDepartment->id,
            'code' => 'required|string|max:10|unique:departments,code,' . $this->editingDepartment->id,
        ]);

        try {
            $this->editingDepartment->update([
                'name' => $this->name,
                'code' => strtoupper($this->code),
            ]);

            Notification::make()
                ->title('Department updated successfully')
                ->success()
                ->send();

            $this->closeEditModal();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to update department')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteDepartment($departmentId)
    {
        try {
            $department = Department::findOrFail($departmentId);

            if ($departmentId == Auth::user()->department->id) {
                Notification::make()
                    ->title('Cannot delete department')
                    ->body('This is the department that you belong to as well.')
                    ->warning()
                    ->send();
                return;
            }

            // Check if department has related records
            if ($department->users()->exists() || $department->courses()->exists()) {
                Notification::make()
                    ->title('Cannot delete department')
                    ->body('This department has associated users or courses. Please remove them first.')
                    ->warning()
                    ->send();
                return;
            }

            $department->delete();

            Notification::make()
                ->title('Department deleted successfully')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to delete department')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        $departments = Department::withCount(['users', 'courses'])
            ->orderBy('name')
            ->paginate(10);

        if (Auth::user()->hasrole('faculty-dean')) 
        {
            // $myFaculty = Auth::user()->department->faculty;

            $departments = Department::where('faculty_id', Auth::user()->myFaculty()->id)
            ->withCount(['users', 'courses'])
            ->orderBy('name')
            ->paginate(10);

        }

        return view('livewire.admin.manage-departments', [
            'departments' => $departments,
        ]);
    }
}