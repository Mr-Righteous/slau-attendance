<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use App\Models\StudentAcademicProgress;
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

    public $importType = 'students'; // Only students now
    public $file;
    public $importing = false;
    public $importResults = [];

    protected $rules = [
        'file' => 'required|file|mimes:csv,txt|max:10240',
        'importType' => 'required|in:students', // Only students
    ];

    public function import()
    {
        $this->validate();
        
        $this->importing = true;
        $this->importResults = [];

        try {
            $path = $this->file->getRealPath();
            $data = array_map(fn($line) => str_getcsv($line, ',', '"', '\\'), file($path));
            $header = array_map('trim', array_shift($data));

            if ($this->importType === 'students') {
                $this->importStudents($data, $header);
            }

            Notification::make()
                ->title('Import completed successfully')
                ->body($this->importResults['success'] . ' students imported, ' . $this->importResults['skipped'] . ' skipped')
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
        set_time_limit(300);

        $expectedHeaders = [
            'registration_number', 'name', 'email', 'department_code', 'course_code',
            'current_year', 'current_semester', 'academic_year', 'gender', 'phone', 'nationality'
        ];

        if ($missing = array_diff($expectedHeaders, $header)) {
            throw new \Exception('Invalid CSV headers. Missing: ' . implode(', ', $missing));
        }

        $success = 0;
        $skipped = 0;
        $errors = [];

        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Collect all department and course codes from the file
        $departmentCodes = collect($data)->pluck(array_search('department_code', $header))->unique()->filter();
        $courseCodes = collect($data)->pluck(array_search('course_code', $header))->unique()->filter();

        // Fetch departments & courses once
        $departments = Department::whereIn('code', $departmentCodes)->pluck('id', 'code');
        $courses = Course::whereIn('code', $courseCodes)->pluck('id', 'code');

        DB::beginTransaction();

        try {
            // Track processed emails and reg numbers in THIS import to avoid duplicates within the same file
            $processedEmails = [];
            $processedRegNos = [];

            foreach ($data as $index => $row) {
                if (empty($row[0])) continue;

                $rowData = array_combine($header, $row);

                $regNumber = trim($rowData['registration_number'] ?? '');
                $name = trim($rowData['name'] ?? '');
                $email = trim($rowData['email'] ?? '');
                $deptCode = trim($rowData['department_code'] ?? '');
                $courseCode = trim($rowData['course_code'] ?? '');
                $currentYear = trim($rowData['current_year'] ?? '1');
                $currentSemester = trim($rowData['current_semester'] ?? '1');
                $academicYear = (int) explode('-', $rowData['academic_year'] ?? now()->year)[0];
                $gender = trim($rowData['gender'] ?? 'other');
                $phone = trim($rowData['phone'] ?? '');
                $nationality = trim($rowData['nationality'] ?? 'Ugandan');

                // Validation
                if (!$regNumber || !$name || !$email) {
                    $skipped++;
                    $errors[] = "Row $index: Missing required fields";
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    $errors[] = "Row $index: Invalid email: $email";
                    continue;
                }

                // Check for duplicates within the same CSV file
                if (in_array($email, $processedEmails)) {
                    $skipped++;
                    $errors[] = "Row $index: Duplicate email in CSV: $email";
                    continue;
                }

                if (in_array($regNumber, $processedRegNos)) {
                    $skipped++;
                    $errors[] = "Row $index: Duplicate registration number in CSV: $regNumber";
                    continue;
                }

                // Check for duplicates in database (real-time check)
                if (User::where('email', $email)->exists()) {
                    $skipped++;
                    $errors[] = "Row $index: Email already exists in database: $email";
                    continue;
                }

                if (Student::where('registration_number', $regNumber)->exists()) {
                    $skipped++;
                    $errors[] = "Row $index: Registration number already exists in database: $regNumber";
                    continue;
                }

                // Department and course lookup
                $departmentId = $departments[$deptCode] ?? null;
                if (!$departmentId) {
                    $skipped++;
                    $errors[] = "Row $index: Department not found: $deptCode";
                    continue;
                }

                $courseId = $courses[$courseCode] ?? null;
                if (!$courseId) {
                    $skipped++;
                    $errors[] = "Row $index: Course not found: $courseCode";
                    continue;
                }

                // Add to processed trackers
                $processedEmails[] = $email;
                $processedRegNos[] = $regNumber;

                // Create user and student
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($regNumber),
                    'department_id' => $departmentId,
                    'password_changed' => false,
                ]);

                $student = Student::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'gender' => $gender,
                    'course_id' => $courseId,
                    'registration_number' => $regNumber,
                    'department_id' => $departmentId,
                    'current_year' => $currentYear,
                    'current_semester' => $currentSemester,
                    'academic_year' => $academicYear,
                    'email' => $email,
                    'phone' => $phone,
                    'nationality' => $nationality,
                ]);

                StudentAcademicProgress::create([
                    'student_id' => $student->id,
                    'course_id' => $courseId,
                    'academic_year' => $academicYear,
                    'year_of_study' => $currentYear,
                    'semester' => $currentSemester,
                    'status' => 'active',
                ]);

                $user->assignRole($studentRole);
                $success++;
            }

            DB::commit();

            $this->importResults = [
                'success' => $success,
                'skipped' => $skipped,
                'errors' => array_slice($errors, 0, 20), // Show first 20 errors only
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function downloadTemplate()
    {
        $content = "registration_number,name,email,department_code,course_code,current_year,current_semester,academic_year,gender,phone,nationality\nBAIT/23U/F0001,John Doe,john.doe@student.slu.ac.ug,IT,BAIT,1,1,2024,male,+256700000001,Ugandan";
        
        $filename = 'students_template.csv';

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        return view('livewire.admin.import-users');
    }
}