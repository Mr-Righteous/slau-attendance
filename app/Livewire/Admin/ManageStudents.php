<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use App\Models\StudentAcademicProgress;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class ManageStudents extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingStudent = null;

    // Form fields
    public $registration_number = '';
    public $name = '';
    public $email = '';
    public $department_id = '';
    public $course_id = '';
    public $current_year = 1;
    public $current_semester = 1;
    public $academic_year = '';
    public $gender = '';
    public $phone = '';
    public $dob = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $zip = '';
    public $country = 'Uganda';

    // Filter fields
    public $search = '';
    public $departmentFilter = '';
    public $courseFilter = '';
    public $yearFilter = '';
    public $semesterFilter = '';

    protected $rules = [
        'registration_number' => 'required|string|max:50|unique:students,registration_number',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'department_id' => 'required|exists:departments,id',
        'course_id' => 'required|exists:courses,id',
        'current_year' => 'required|integer|min:1|max:6',
        'current_semester' => 'required|integer|in:1,2',
        'academic_year' => 'required|integer|min:2020|max:2030',
        'gender' => 'required|in:male,female,other',
        'phone' => 'nullable|string|max:20',
        'dob' => 'nullable|date',
        'address' => 'nullable|string|max:500',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'zip' => 'nullable|string|max:20',
        'country' => 'nullable|string|max:100',
    ];

    public function mount()
    {
        $this->academic_year = now()->year;
    }

    public function openCreateModal()
    {
        $this->dispatch('open-modal', id:'create-student');

        $this->reset([
            'registration_number', 'name', 'email', 'department_id', 'course_id',
            'current_year', 'current_semester', 'academic_year', 'gender', 'phone',
            'dob', 'address', 'city', 'state', 'zip', 'country'
        ]);
        $this->academic_year = now()->year;
        $this->current_year = 1;
        $this->current_semester = 1;
        $this->gender = 'male';
        $this->country = 'Uganda';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->dispatch('close-modal', id:'create-student');
        $this->showCreateModal = false;
        $this->resetErrorBag();
    }

    public function createStudent()
    {
        $this->validate();

        try {
            // Create user account
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->registration_number), // Use registration number as initial password
                'department_id' => $this->department_id,
                'password_changed' => false,
            ]);

            // Assign student role
            $studentRole = Role::where('name', 'student')->first();
            $user->assignRole($studentRole);

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'name' => $this->name,
                'gender' => $this->gender,
                'dob' => $this->dob,
                'course_id' => $this->course_id,
                'registration_number' => $this->registration_number,
                'department_id' => $this->department_id,
                'current_year' => $this->current_year,
                'academic_year' => $this->academic_year,
                'current_semester' => $this->current_semester,
                'program_id' => $this->course_id,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'zip' => $this->zip,
                'country' => $this->country,
            ]);

            // Create academic progress record
            StudentAcademicProgress::create([
                'student_id' => $student->id,
                'course_id' => $this->course_id,
                'academic_year' => $this->academic_year,
                'year_of_study' => $this->current_year,
                'semester' => $this->current_semester,
                'status' => 'active',
            ]);

            Notification::make()
                ->title('Student created successfully')
                ->body('Student account and academic progress record created.')
                ->success()
                ->send();

            $this->closeCreateModal();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to create student')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function editStudent($studentId)
    {
        $this->editingStudent = Student::with(['user', 'course', 'department', 'academicProgress'])->findOrFail($studentId);
        $this->registration_number = $this->editingStudent->registration_number;
        $this->name = $this->editingStudent->name;
        $this->email = $this->editingStudent->email;
        $this->department_id = $this->editingStudent->department_id;
        $this->course_id = $this->editingStudent->course_id;
        $this->current_year = $this->editingStudent->current_year;
        $this->current_semester = $this->editingStudent->current_semester;
        $this->academic_year = $this->editingStudent->academic_year;
        $this->gender = $this->editingStudent->gender;
        $this->phone = $this->editingStudent->phone;
        $this->dob = $this->editingStudent->dob?->format('Y-m-d');
        $this->address = $this->editingStudent->address;
        $this->city = $this->editingStudent->city;
        $this->state = $this->editingStudent->state;
        $this->zip = $this->editingStudent->zip;
        $this->country = $this->editingStudent->country;
        $this->showEditModal = true;
        $this->dispatch('open-modal', id:'edit-student');
    }

    public function closeEditModal()
    {
        $this->dispatch('close-modal', id:'edit-student');
        $this->showEditModal = false;
        $this->editingStudent = null;
        $this->resetErrorBag();
    }

    public function updateStudent()
    {
        $this->validate([
            'registration_number' => 'required|string|max:50|unique:students,registration_number,' . $this->editingStudent->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->editingStudent->user_id,
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'current_year' => 'required|integer|min:1|max:6',
            'current_semester' => 'required|integer|in:1,2',
            'academic_year' => 'required|integer|min:2020|max:2030',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        try {
            // Update user
            $this->editingStudent->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'department_id' => $this->department_id,
            ]);

            // Update student
            $this->editingStudent->update([
                'name' => $this->name,
                'gender' => $this->gender,
                'dob' => $this->dob,
                'course_id' => $this->course_id,
                'registration_number' => $this->registration_number,
                'department_id' => $this->department_id,
                'current_year' => $this->current_year,
                'academic_year' => $this->academic_year,
                'current_semester' => $this->current_semester,
                'program_id' => $this->course_id,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'zip' => $this->zip,
                'country' => $this->country,
            ]);

            // Update or create academic progress
            $academicProgress = $this->editingStudent->academicProgress()
                ->where('academic_year', $this->academic_year)
                ->first();

            if ($academicProgress) {
                $academicProgress->update([
                    'year_of_study' => $this->current_year,
                    'semester' => $this->current_semester,
                ]);
            } else {
                StudentAcademicProgress::create([
                    'student_id' => $this->editingStudent->id,
                    'course_id' => $this->course_id,
                    'academic_year' => $this->academic_year,
                    'year_of_study' => $this->current_year,
                    'semester' => $this->current_semester,
                    'status' => 'active',
                ]);
            }

            Notification::make()
                ->title('Student updated successfully')
                ->success()
                ->send();

            $this->closeEditModal();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to update student')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteStudent($studentId)
    {
        try {
            $student = Student::with(['user', 'attendanceRecords'])->findOrFail($studentId);
            
            // Check if student has attendance records
            if ($student->attendanceRecords()->exists()) {
                Notification::make()
                    ->title('Cannot delete student')
                    ->body('This student has attendance records. Please contact system administrator.')
                    ->warning()
                    ->send();
                return;
            }

            // Delete academic progress records
            $student->academicProgress()->delete();

            // Delete user account
            $student->user->delete();

            // Delete student record
            $student->delete();

            Notification::make()
                ->title('Student deleted successfully')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to delete student')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetPassword($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $student->user->update([
                'password' => bcrypt($student->registration_number),
                'password_changed' => false,
            ]);

            Notification::make()
                ->title('Password reset successfully')
                ->body('Password has been reset to registration number: ' . $student->registration_number)
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to reset password')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        $students = Student::forUserRole(Auth::user())->with(['user', 'course', 'department', 'academicProgress'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('registration_number', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->departmentFilter, function ($query) {
                $query->where('department_id', $this->departmentFilter);
            })
            ->when($this->courseFilter, function ($query) {
                $query->where('course_id', $this->courseFilter);
            })
            ->when($this->yearFilter, function ($query) {
                $query->where('current_year', $this->yearFilter);
            })
            ->when($this->semesterFilter, function ($query) {
                $query->where('current_semester', $this->semesterFilter);
            })
            ->orderBy('registration_number')
            ->paginate(15);

        $departments = Department::orderBy('name')->get();
        $courses = Course::forUserRole(Auth::user())->orderBy('name')->get();

        return view('livewire.admin.manage-students', [
            'students' => $students,
            'departments' => $departments,
            'courses' => $courses,
        ]);
    }
}