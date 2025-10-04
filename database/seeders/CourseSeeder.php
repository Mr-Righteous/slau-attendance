<?php

namespace Database\Seeders;

use App\Models\CourseUnit;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Exception;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::pluck('id', 'code');
        $lecturers = User::role('lecturer')->pluck('id');

        if ($lecturers->isEmpty()) {
            throw new Exception('No lecturers found. Run UserSeeder first.');
        }

        $courses = [
            ['code' => 'CS101', 'name' => 'Introduction to Programming', 'department_code' => 'CS'],
            ['code' => 'CS201', 'name' => 'Data Structures and Algorithms', 'department_code' => 'CS'],
            ['code' => 'IT102', 'name' => 'Web Development Fundamentals', 'department_code' => 'IT'],
            ['code' => 'IT202', 'name' => 'Network Security', 'department_code' => 'IT'],
            ['code' => 'BBA101', 'name' => 'Principles of Management', 'department_code' => 'BUS'],
            ['code' => 'BBA301', 'name' => 'Marketing Management', 'department_code' => 'BUS'],
            ['code' => 'EE101', 'name' => 'Introduction to Circuits', 'department_code' => 'EE'],
            ['code' => 'PSY101', 'name' => 'Introduction to Psychology', 'department_code' => 'PSY'],
        ];

        foreach ($courses as $courseData) {
            $departmentId = $departments->get($courseData['department_code']);
            if ($departmentId) {
                CourseUnit::firstOrCreate(
                    ['code' => $courseData['code']],
                    [
                        'name' => $courseData['name'],
                        'department_id' => $departmentId,
                        'lecturer_id' => $lecturers->random(),
                        'credits' => rand(2, 4),
                        'semester' => rand(1, 2),
                        'academic_year' => '2024/2025',
                    ]
                );
            }
        }
    }
}
