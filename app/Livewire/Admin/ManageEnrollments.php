<?php

// ============================================
// COMPONENT: ManageEnrollments.php
// app/Livewire/Admin/ManageEnrollments.php
// ============================================

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Department;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ManageEnrollments extends Component
{
    use WithPagination;

    public $showEnrollModal = false;
    public $showBulkEnrollModal = false;
    
    public $selectedCourse;
    public $selectedCourseId;
    public $selectedStudents = [];
    public $searchStudent = '';
    public $bulkStudentIds = '';
    
    // Filters
    public $filterCourse = '';
    public $filterDepartment = '';
    public $searchEnrollment = '';

    public function openEnrollModal($courseId)
    {
        $this->selectedCourseId = $courseId;
        $this->selectedCourse = CourseUnit::with('department', 'lecturer')->find($courseId);
        $this->selectedStudents = [];
        $this->searchStudent = '';
        $this->showEnrollModal = true;
    }

    public function openBulkEnrollModal($courseId)
    {
        $this->selectedCourseId = $courseId;
        $this->selectedCourse = CourseUnit::with('department', 'lecturer')->find($courseId);
        $this->bulkStudentIds = '';
        $this->showBulkEnrollModal = true;
    }

    public function closeModals()
    {
        $this->showEnrollModal = false;
        $this->showBulkEnrollModal = false;
        $this->reset(['selectedCourseId', 'selectedCourse', 'selectedStudents', 'searchStudent', 'bulkStudentIds']);
    }

    public function toggleStudent($studentId)
    {
        if (in_array($studentId, $this->selectedStudents)) {
            $this->selectedStudents = array_diff($this->selectedStudents, [$studentId]);
        } else {
            $this->selectedStudents[] = $studentId;
        }
    }

    public function enrollSelectedStudents()
    {
        if (empty($this->selectedStudents)) {
            Notification::make()
                ->title('No students selected')
                ->warning()
                ->send();
            return;
        }

        DB::beginTransaction();

        try {
            $enrolled = 0;
            $skipped = 0;

            foreach ($this->selectedStudents as $studentId) {
                // Check if already enrolled
                if (Enrollment::where('course_id', $this->selectedCourseId)
                    ->where('student_id', $studentId)
                    ->exists()) {
                    $skipped++;
                    continue;
                }

                Enrollment::create([
                    'course_id' => $this->selectedCourseId,
                    'student_id' => $studentId,
                    'enrolled_at' => now(),
                ]);

                $enrolled++;
            }

            DB::commit();

            Notification::make()
                ->title('Students enrolled successfully')
                ->body("$enrolled enrolled, $skipped already enrolled")
                ->success()
                ->send();

            $this->closeModals();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error enrolling students')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function bulkEnrollStudents()
    {
        $this->validate([
            'bulkStudentIds' => 'required|string',
        ]);

        // Parse registration numbers (comma or newline separated)
        $regNumbers = preg_split('/[\s,]+/', $this->bulkStudentIds, -1, PREG_SPLIT_NO_EMPTY);
        $regNumbers = array_map('trim', $regNumbers);

        if (empty($regNumbers)) {
            Notification::make()
                ->title('No registration numbers provided')
                ->warning()
                ->send();
            return;
        }

        DB::beginTransaction();

        try {
            $enrolled = 0;
            $skipped = 0;
            $notFound = [];

            foreach ($regNumbers as $regNumber) {
                $student = User::where('registration_number', $regNumber)
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'student');
                    })
                    ->first();

                if (!$student) {
                    $notFound[] = $regNumber;
                    continue;
                }

                // Check if already enrolled
                if (Enrollment::where('course_id', $this->selectedCourseId)
                    ->where('student_id', $student->id)
                    ->exists()) {
                    $skipped++;
                    continue;
                }

                Enrollment::create([
                    'course_id' => $this->selectedCourseId,
                    'student_id' => $student->id,
                    'enrolled_at' => now(),
                ]);

                $enrolled++;
            }

            DB::commit();

            $message = "$enrolled enrolled, $skipped already enrolled";
            if (!empty($notFound)) {
                $message .= ", " . count($notFound) . " not found";
            }

            Notification::make()
                ->title('Bulk enrollment completed')
                ->body($message)
                ->success()
                ->send();

            $this->closeModals();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error in bulk enrollment')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function unenroll($enrollmentId)
    {
        try {
            $enrollment = Enrollment::findOrFail($enrollmentId);
            
            // Check if student has attendance records
            $hasAttendance = DB::table('attendance_records')
                ->whereIn('class_session_id', function ($query) use ($enrollment) {
                    $query->select('id')
                        ->from('class_sessions')
                        ->where('course_id', $enrollment->course_id);
                })
                ->where('student_id', $enrollment->student_id)
                ->exists();

            if ($hasAttendance) {
                Notification::make()
                    ->title('Cannot unenroll student')
                    ->body('Student has attendance records. Delete attendance first.')
                    ->warning()
                    ->send();
                return;
            }

            $enrollment->delete();

            Notification::make()
                ->title('Student unenrolled successfully')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error unenrolling student')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function updatingFilterCourse()
    {
        $this->resetPage();
    }

    public function updatingSearchEnrollment()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Get available students for enrollment modal (not already enrolled)
        $availableStudents = [];
        if ($this->showEnrollModal && $this->selectedCourseId) {
            $enrolledIds = Enrollment::where('course_id', $this->selectedCourseId)
                ->pluck('student_id');

            $availableStudents = User::whereHas('roles', function ($query) {
                $query->where('name', 'student');
            })
            ->whereNotIn('id', $enrolledIds)
            ->when($this->searchStudent, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchStudent . '%')
                      ->orWhere('registration_number', 'like', '%' . $this->searchStudent . '%')
                      ->orWhere('email', 'like', '%' . $this->searchStudent . '%');
                });
            })
            ->with('department')
            ->orderBy('name')
            ->limit(50)
            ->get();
        }

        // Get enrollments with filters
        $enrollments = Enrollment::query()
            ->with(['student.department', 'course.department', 'course.lecturer'])
            ->when($this->filterCourse, function ($query) {
                $query->where('course_id', $this->filterCourse);
            })
            ->when($this->filterDepartment, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('department_id', $this->filterDepartment);
                });
            })
            ->when($this->searchEnrollment, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchEnrollment . '%')
                      ->orWhere('registration_number', 'like', '%' . $this->searchEnrollment . '%');
                })
                ->orWhereHas('course', function ($q) {
                    $q->where('code', 'like', '%' . $this->searchEnrollment . '%')
                      ->orWhere('name', 'like', '%' . $this->searchEnrollment . '%');
                });
            })
            ->orderBy('enrolled_at', 'desc')
            ->paginate(20);

        $courses = CourseUnit::with('department')->orderBy('code')->get();
        $departments = Department::orderBy('name')->get();

        return view('livewire.admin.manage-enrollments', [
            'enrollments' => $enrollments,
            'courses' => $courses,
            'departments' => $departments,
            'availableStudents' => $availableStudents,
        ]);
    }
}