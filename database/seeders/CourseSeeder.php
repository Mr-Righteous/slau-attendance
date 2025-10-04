<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch department IDs by code (assumes DepartmentSeeder has been run)
        $departments = [
            'CS' => DB::table('departments')->where('code', 'CS')->value('id'),
            'IT' => DB::table('departments')->where('code', 'IT')->value('id'),
            'MATH' => DB::table('departments')->where('code', 'MATH')->value('id'),
            'PHYS' => DB::table('departments')->where('code', 'PHYS')->value('id'),
            'CHEM' => DB::table('departments')->where('code', 'CHEM')->value('id'),
            'BIO' => DB::table('departments')->where('code', 'BIO')->value('id'),
            'ENG' => DB::table('departments')->where('code', 'ENG')->value('id'),
            'HIST' => DB::table('departments')->where('code', 'HIST')->value('id'),
            'BUS' => DB::table('departments')->where('code', 'BUS')->value('id'),
            'ECON' => DB::table('departments')->where('code', 'ECON')->value('id'),
            'ME' => DB::table('departments')->where('code', 'ME')->value('id'),
            'EE' => DB::table('departments')->where('code', 'EE')->value('id'),
            'CE' => DB::table('departments')->where('code', 'CE')->value('id'),
            'PSY' => DB::table('departments')->where('code', 'PSY')->value('id'),
            'SOC' => DB::table('departments')->where('code', 'SOC')->value('id'),
        ];

        // Validate that all required departments exist
        $missingDepartments = collect($departments)->filter(fn($id) => is_null($id))->keys();
        if ($missingDepartments->isNotEmpty()) {
            throw new Exception('Missing departments with codes: ' . $missingDepartments->implode(', ') . '. Run DepartmentSeeder first.');
        }

        // Optional: Fetch lecturer IDs (users with 'lecturer' role). Uncomment and adjust if you have seeded lecturers.
        $lecturers = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'lecturer')
            ->pluck('users.id')
            ->toArray();
        
        // Randomly assign lecturers (or set to null if no lecturers exist)
        $randomLecturer = !empty($lecturers) ? $lecturers[array_rand($lecturers)] : null;

        $courses = [
            // Computer Science (CS)
            [
                'code' => 'CS101',
                'name' => 'Introduction to Programming',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['CS'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],
            [
                'code' => 'CS201',
                'name' => 'Data Structures and Algorithms',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['CS'],
                'semester' => '2',
                'academic_year' => '2024/2025',
                'credits' => 4,
            ],
            [
                'code' => 'CS301',
                'name' => 'Database Systems',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['CS'],
                'semester' => 'Summer',
                'academic_year' => '2023/2024',
                'credits' => 3,
            ],

            // Information Technology (IT)
            [
                'code' => 'IT102',
                'name' => 'Web Development Fundamentals',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['IT'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],
            [
                'code' => 'IT202',
                'name' => 'Network Security',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['IT'],
                'semester' => '2',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],

            // Mathematics (MATH)
            [
                'code' => 'MATH101',
                'name' => 'Calculus I',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['MATH'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 4,
            ],
            [
                'code' => 'MATH201',
                'name' => 'Linear Algebra',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['MATH'],
                'semester' => '2',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],

            // Physics (PHYS)
            [
                'code' => 'PHYS101',
                'name' => 'General Physics I',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['PHYS'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 4,
            ],
            [
                'code' => 'PHYS202',
                'name' => 'Quantum Mechanics',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['PHYS'],
                'semester' => '2',
                'academic_year' => '2023/2024',
                'credits' => 3,
            ],

            // Chemistry (CHEM)
            [
                'code' => 'CHEM101',
                'name' => 'General Chemistry',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['CHEM'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 4,
            ],

            // Biology (BIO)
            [
                'code' => 'BIO101',
                'name' => 'Cell Biology',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['BIO'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],

            // English (ENG)
            [
                'code' => 'ENG101',
                'name' => 'English Composition',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['ENG'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],

            // Business (BUS)
            [
                'code' => 'BUS101',
                'name' => 'Principles of Management',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['BUS'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],
            [
                'code' => 'BUS301',
                'name' => 'Marketing Management',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['BUS'],
                'semester' => 'Summer',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],

            // Economics (ECON)
            [
                'code' => 'ECON101',
                'name' => 'Microeconomics',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['ECON'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],

            // Engineering (ME, EE, CE)
            [
                'code' => 'ME101',
                'name' => 'Engineering Mechanics',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['ME'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],
            [
                'code' => 'EE201',
                'name' => 'Circuit Analysis',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['EE'],
                'semester' => '2',
                'academic_year' => '2024/2025',
                'credits' => 4,
            ],
            [
                'code' => 'CE301',
                'name' => 'Structural Engineering',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['CE'],
                'semester' => '1',
                'academic_year' => '2023/2024',
                'credits' => 3,
            ],

            // Social Sciences (PSY, SOC)
            [
                'code' => 'PSY101',
                'name' => 'Introduction to Psychology',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['PSY'],
                'semester' => '1',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],
            [
                'code' => 'SOC201',
                'name' => 'Social Theory',
                'lecturer_id' => $randomLecturer,
                'department_id' => $departments['SOC'],
                'semester' => '2',
                'academic_year' => '2024/2025',
                'credits' => 3,
            ],
        ];

        // Insert courses
        DB::table('courses')->insert($courses);

        $this->command->info('Inserted ' . count($courses) . ' sample courses.');
    }
}