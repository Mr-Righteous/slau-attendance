<?php

// ============================================
// COMPONENT: ImportUsers.php
// app/Livewire/Admin/ImportUsers.php
// ============================================

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

class ImportUsers extends Component
{
    use WithFileUploads;

    public $importType = 'students'; // students, lecturers, programs, enrollments
    public $file;
    public $importing = false;
    public $importResults = [];

    protected $rules = [
        'file' => 'required|file|mimes:csv,txt|max:10240',
        'importType' => 'required|in:students,lecturers,programs,enrollments',
    ];

    public function import()
    {
        $this->validate();
        
        $this->importing = true;
        $this->importResults = [];

        try {
            $path = $this->file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $header = array_map('trim', array_shift($data));

            switch ($this->importType) {
                case 'students':
                    $this->importStudents($data, $header);
                    break;
                case 'lecturers':
                    $this->importLecturers($data, $header);
                    break;
                case 'programs':
                    $this->importPrograms($data, $header);
                    break;
                case 'enrollments':
                    $this->importEnrollments($data, $header);
                    break;
            }

            Notification::make()
                ->title('Import completed successfully')
                ->body($this->importResults['success'] . ' records imported, ' . $this->importResults['skipped'] . ' skipped')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Import failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        $this->importing = false;
        $this->reset('file');
    }

    protected function importStudents($data, $header)
    {
        // Expected columns: registration_number, name, email, department_code, program_code, current_year, current_semester, academic_year
        $success = 0;
        $skipped = 0;
        $errors = [];

        $studentRole = Role::firstOrCreate(['name' => 'student']);

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                if (empty($row[0])) continue;

                $rowData = array_combine($header, $row);
                $regNumber = trim($rowData['registration_number'] ?? '');
                $name = trim($rowData['name'] ?? '');
                $email = trim($rowData['email'] ?? '');
                $deptCode = trim($rowData['department_code'] ?? '');
                $programCode = trim($rowData['program_code'] ?? '');
                $currentYear = trim($rowData['current_year'] ?? '');
                $currentSemester = trim($rowData['current_semester'] ?? '');
                $academicYear = trim($rowData['academic_year'] ?? '');

                // Validation
                if (empty($regNumber) || empty($name) || empty($email)) {
                    $skipped++;
                    $errors[] = "Skipped row: Missing required fields (reg: $regNumber)";
                    continue;
                }

                // Check if user exists
                if (User::where('email', $email)->exists()) {
                    $skipped++;
                    $errors[] = "Skipped: Email $email already exists";
                    continue;
                }

                if (User::where('registration_number', $regNumber)->exists()) {
                    $skipped++;
                    $errors[] = "Skipped: Registration number $regNumber already exists";
                    continue;
                }

                // Get department and program
                $department = Department::where('code', $deptCode)->first();
                $program = \App\Models\Program::where('code', $programCode)->first();

                // Create user
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($regNumber), // Default password is reg number
                    'registration_number' => $regNumber,
                    'department_id' => $department?->id,
                    'password_changed' => false,
                ]);

                // Create student record
                \App\Models\Student::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'email' => $email,
                    'registration_number' => $regNumber,
                    'department_id' => $department?->id,
                    'program_id' => $program?->id,
                    'current_year' => $currentYear ?: null,
                    'current_semester' => $currentSemester ?: null,
                    'academic_year' => $academicYear ?: null,
                ]);

                $user->assignRole($studentRole);
                $success++;
            }

            DB::commit();

            $this->importResults = [
                'success' => $success,
                'skipped' => $skipped,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function importLecturers($data, $header)
    {
        // Expected columns: staff_number, name, email, department_code
        $success = 0;
        $skipped = 0;
        $errors = [];

        $lecturerRole = Role::firstOrCreate(['name' => 'lecturer']);

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                if (empty($row[0])) continue;

                $rowData = array_combine($header, $row);
                $staffNumber = trim($rowData['staff_number'] ?? '');
                $name = trim($rowData['name'] ?? '');
                $email = trim($rowData['email'] ?? '');
                $deptCode = trim($rowData['department_code'] ?? '');

                if (empty($staffNumber) || empty($name) || empty($email)) {
                    $skipped++;
                    $errors[] = "Skipped row: Missing required fields (staff: $staffNumber)";
                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $skipped++;
                    $errors[] = "Skipped: Email $email already exists";
                    continue;
                }

                if (User::where('registration_number', $staffNumber)->exists()) {
                    $skipped++;
                    $errors[] = "Skipped: Staff number $staffNumber already exists";
                    continue;
                }

                $department = Department::where('code', $deptCode)->first();

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($staffNumber),
                    'registration_number' => $staffNumber,
                    'department_id' => $department?->id,
                    'password_changed' => false,
                ]);

                $user->assignRole($lecturerRole);
                $success++;
            }

            DB::commit();

            $this->importResults = [
                'success' => $success,
                'skipped' => $skipped,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function importPrograms($data, $header)
    {
        // Expected columns: program_code, program_name, department_code, duration_years
        $success = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                if (empty($row[0])) continue;

                $rowData = array_combine($header, $row);
                $programCode = trim($rowData['program_code'] ?? '');
                $programName = trim($rowData['program_name'] ?? '');
                $deptCode = trim($rowData['department_code'] ?? '');
                $duration = trim($rowData['duration_years'] ?? '4');

                if (empty($programCode) || empty($programName)) {
                    $skipped++;
                    $errors[] = "Skipped row: Missing required fields (code: $programCode)";
                    continue;
                }

                if (Program::where('code', $programCode)->exists()) {
                    $skipped++;
                    $errors[] = "Skipped: Program code $programCode already exists";
                    continue;
                }

                $department = Department::where('code', $deptCode)->first();

                if (!$department) {
                    $skipped++;
                    $errors[] = "Skipped: Department $deptCode not found for program $programCode";
                    continue;
                }

                Program::create([
                    'name' => $programName,
                    'code' => $programCode,
                    'department_id' => $department->id,
                    'duration_years' => $duration,
                    'description' => $programName,
                ]);

                $success++;
            }

            DB::commit();

            $this->importResults = [
                'success' => $success,
                'skipped' => $skipped,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function importEnrollments($data, $header)
    {
        // Expected columns: registration_number, course_code
        $success = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                if (empty($row[0])) continue;

                $rowData = array_combine($header, $row);
                $regNumber = trim($rowData['registration_number'] ?? '');
                $courseCode = trim($rowData['course_code'] ?? '');

                if (empty($regNumber) || empty($courseCode)) {
                    $skipped++;
                    continue;
                }

                $student = User::where('registration_number', $regNumber)->first();
                $courseUnit = CourseUnit::where('code', $courseCode)->first();

                if (!$student) {
                    $skipped++;
                    $errors[] = "Skipped: Student $regNumber not found";
                    continue;
                }

                if (!$courseUnit) {
                    $skipped++;
                    $errors[] = "Skipped: Course Unit $courseCode not found";
                    continue;
                }

                // Check if already enrolled
                if (Enrollment::where('student_id', $student->id)
                    ->where('course_id', $courseUnit->id)
                    ->exists()) {
                    $skipped++;
                    continue;
                }

                Enrollment::create([
                    'student_id' => $student->id,
                    'course_id' => $courseUnit->id,
                    'enrolled_at' => now(),
                ]);

                $success++;
            }

            DB::commit();

            $this->importResults = [
                'success' => $success,
                'skipped' => $skipped,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function downloadTemplate()
    {
        $templates = [
            'students' => "registration_number,name,email,department_code\nS2024001,John Doe,john@example.com,CS\nS2024002,Jane Smith,jane@example.com,IT",
            'lecturers' => "staff_number,name,email,department_code\nL001,Dr. Smith,smith@example.com,CS\nL002,Prof. Jones,jones@example.com,IT",
            'programs' => "program_code,program_name,department_code,duration_years\nPROG101,Bachelor of Science in Computer Science,CS,4",
            'enrollments' => "registration_number,course_code\nS2024001,CS101\nS2024001,IT201\nS2024002,CS101",
        ];

        $content = $templates[$this->importType];
        $filename = $this->importType . '_template.csv';

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        return view('livewire.admin.import-users');
    }
}