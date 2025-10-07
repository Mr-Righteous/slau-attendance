<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\CourseUnit;
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

    public $importType = 'students'; // students, lecturers, courses, course_units
    public $file;
    public $importing = false;
    public $importResults = [];

    protected $rules = [
        'file' => 'required|file|mimes:csv,txt|max:10240',
        'importType' => 'required|in:students,lecturers,courses,course_units,departments',
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

            switch ($this->importType) {
                case 'students':
                    $this->importStudents($data, $header);
                    break;
                case 'lecturers':
                    $this->importLecturers($data, $header);
                    break;
                case 'courses':
                    $this->importCourses($data, $header);
                    break;
                case 'course_units':
                    $this->importCourseUnits($data, $header);
                    break;
                case 'departments': // Add this case
                    $this->importDepartments($data, $header);
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

    protected function importDepartments($data, $header)
{
    $expectedHeaders = ['code', 'name'];
    if (array_diff($expectedHeaders, $header)) {
        throw new \Exception('Invalid CSV headers. Expected: ' . implode(', ', $expectedHeaders));
    }

    $success = 0;
    $skipped = 0;
    $errors = [];

    DB::beginTransaction();

    try {
        foreach ($data as $row) {
            if (empty($row[0])) continue;

            $rowData = array_combine($header, $row);
            $code = trim($rowData['code'] ?? '');
            $name = trim($rowData['name'] ?? '');

            // Validation
            if (empty($code) || empty($name)) {
                $skipped++;
                $errors[] = "Skipped row: Missing required fields (code: $code)";
                continue;
            }

            if (Department::where('code', $code)->exists()) {
                $skipped++;
                $errors[] = "Skipped: Department code $code already exists";
                continue;
            }

            if (Department::where('name', $name)->exists()) {
                $skipped++;
                $errors[] = "Skipped: Department name $name already exists";
                continue;
            }

            // Create department
            Department::create([
                'code' => strtoupper($code),
                'name' => $name,
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

    protected function importStudents($data, $header)
    {
        set_time_limit(300);

        $expectedHeaders = [
            'registration_number', 'name', 'email', 'department_code', 'course_code',
            'current_year', 'current_semester', 'academic_year', 'gender', 'phone'
        ];

        // Validate headers
        if ($missing = array_diff($expectedHeaders, $header)) {
            throw new \Exception('Invalid CSV headers. Missing: ' . implode(', ', $missing));
        }

        $success = 0;
        $skipped = 0;
        $errors = [];

        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Collect all department and course codes from the file first
        $departmentCodes = collect($data)->pluck(array_search('department_code', $header))->unique()->filter();
        $courseCodes = collect($data)->pluck(array_search('course_code', $header))->unique()->filter();

        // Fetch departments & courses once
        $departments = Department::whereIn('code', $departmentCodes)->pluck('id', 'code');
        $courses = Course::whereIn('code', $courseCodes)->pluck('id', 'code');

        // Pre-fetch existing users and students for duplicates
        $emails = collect($data)->pluck(array_search('email', $header))->unique()->filter();
        $regNumbers = collect($data)->pluck(array_search('registration_number', $header))->unique()->filter();

        $existingEmails = User::whereIn('email', $emails)->pluck('email')->toArray();
        $existingRegNos = Student::whereIn('registration_number', $regNumbers)->pluck('registration_number')->toArray();

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                if (empty($row[0])) continue;

                $rowData = array_combine($header, $row);

                $regNumber = trim($rowData['registration_number'] ?? '');
                $name = trim($rowData['name'] ?? '');
                $email = trim($rowData['email'] ?? '');
                $deptCode = trim($rowData['department_code'] ?? '');
                $courseCode = trim($rowData['course_code'] ?? '');
                $currentYear = trim($rowData['current_year'] ?? '1');
                $currentSemester = trim($rowData['current_semester'] ?? '1');
                $academicYear = trim($rowData['academic_year'] ?? now()->year);
                $gender = trim($rowData['gender'] ?? 'other');
                $phone = trim($rowData['phone'] ?? '');

                // Quick validation before touching DB
                if (!$regNumber || !$name || !$email) {
                    $skipped++;
                    $errors[] = "Missing required fields (Reg: $regNumber)";
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    $errors[] = "Invalid email: $email";
                    continue;
                }

                if (in_array($email, $existingEmails)) {
                    $skipped++;
                    $errors[] = "Duplicate email: $email";
                    continue;
                }

                if (in_array($regNumber, $existingRegNos)) {
                    $skipped++;
                    $errors[] = "Duplicate registration: $regNumber";
                    continue;
                }

                // Department and course lookup
                $departmentId = $departments[$deptCode] ?? null;
                if (!$departmentId) {
                    $skipped++;
                    $errors[] = "Department not found: $deptCode ($regNumber)";
                    continue;
                }

                $courseId = $courses[$courseCode] ?? null;
                if (!$courseId) {
                    $skipped++;
                    $errors[] = "Course not found: $courseCode ($regNumber)";
                    continue;
                }

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
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function importLecturers($data, $header)
    {
        $expectedHeaders = ['staff_number', 'name', 'email', 'department_code'];
        if (array_diff($expectedHeaders, $header)) {
            throw new \Exception('Invalid CSV headers. Expected: ' . implode(', ', $expectedHeaders));
        }

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

                $department = Department::where('code', $deptCode)->first();

                // Create lecturer user (no registration_number in users table)
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($staffNumber),
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

    protected function importCourses($data, $header)
    {
        $expectedHeaders = ['code', 'name', 'department_code', 'duration_years'];
        if (array_diff($expectedHeaders, $header)) {
            throw new \Exception('Invalid CSV headers. Expected: ' . implode(', ', $expectedHeaders));
        }
    
        $success = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                if (empty($row[0])) continue;

                $rowData = array_combine($header, $row);
                $courseCode = trim($rowData['code'] ?? '');
                $courseName = trim($rowData['name'] ?? '');
                $deptCode = trim($rowData['department_code'] ?? '');
                $duration = trim($rowData['duration_years'] ?? '4');

                if (empty($courseCode) || empty($courseName)) {
                    $skipped++;
                    $errors[] = "Skipped row: Missing required fields (code: $courseCode)";
                    continue;
                }

                if (Course::where('code', $courseCode)->exists()) {
                    $skipped++;
                    $errors[] = "Skipped: Course code $courseCode already exists";
                    continue;
                }

                $department = Department::where('code', $deptCode)->first();

                if (!$department) {
                    $skipped++;
                    $errors[] = "Skipped: Department $deptCode not found for course $courseCode";
                    continue;
                }

                Course::create([
                    'name' => $courseName,
                    'code' => $courseCode,
                    'department_id' => $department->id,
                    'duration_years' => $duration,
                    'description' => $courseName, // Using name as description if not provided
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

    protected function importCourseUnits($data, $header)
    {
        $expectedHeaders = ['code', 'name', 'course_code', 'lecturer_email', 'credits', 'semester', 'academic_year'];
        if (array_diff($expectedHeaders, $header)) {
            throw new \Exception('Invalid CSV headers. Expected: ' . implode(', ', $expectedHeaders) . ' and received ' . implode(', ', $header));
        }

        $success = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                if (empty($row[0])) continue;

                $rowData = array_combine($header, $row);
                $code = trim($rowData['code'] ?? '');
                $name = trim($rowData['name'] ?? '');
                $courseCode = trim($rowData['course_code'] ?? '');
                $lecturerEmail = trim($rowData['lecturer_email'] ?? '');
                $credits = trim($rowData['credits'] ?? '3');
                $semester = trim($rowData['semester'] ?? '1');
                $academicYear = trim($rowData['academic_year'] ?? now()->year);

                if (empty($code) || empty($name) || empty($courseCode)) {
                    $skipped++;
                    $errors[] = "Skipped row: Missing required fields (code: $code)";
                    continue;
                }

                if (CourseUnit::where('code', $code)->exists()) {
                    $skipped++;
                    $errors[] = "Skipped: Course unit code $code already exists";
                    continue;
                }

                $course = Course::where('code', $courseCode)->first();
                if (!$course) {
                    $skipped++;
                    $errors[] = "Skipped: Course $courseCode not found for course unit $code";
                    continue;
                }

                $lecturer = null;
                if (!empty($lecturerEmail)) {
                    $lecturer = User::where('email', $lecturerEmail)->first();
                    if (!$lecturer) {
                        $skipped++;
                        $errors[] = "Skipped: Lecturer $lecturerEmail not found for course unit $code";
                        continue;
                    }
                }

                $courseUnit = CourseUnit::create([
                    'code' => $code,
                    'course_id' => $course->id,
                    'name' => $name,
                    'description' => $name,
                    'department_id' => $course->department_id,
                    'lecturer_id' => $lecturer?->id,
                    'credits' => $credits,
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                ]);

                // Attach to course with default year/semester
                $courseUnit->courses()->attach($course->id, [
                    'default_year' => 1, // Default to year 1
                    'default_semester' => $semester,
                    'is_core' => true,
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
            'departments' => "code,name\nCS,Computer Science\nBUS,Business Administration\nIT,Information Technology",
            'students' => "registration_number,name,email,department_code,course_code,current_year,current_semester,academic_year,gender,phone\nBAIT/23U/F0001,John Doe,john.doe@student.slu.ac.ug,CS,BAIT,1,1,2024,male,+256700000001",
            'lecturers' => "staff_number,name,email,department_code\nL001,Dr. John Smith,john.smith@slu.ac.ug,CS",
            'courses' => "code,name,department_code,duration_years\nBAIT,Bachelor of Information Technology,CS,3",
            'course_units' => "code,name,course_code,lecturer_email,credits,semester,academic_year\nCS101,Introduction to Programming,BAIT,john.smith@slu.ac.ug,3,1,2024",
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