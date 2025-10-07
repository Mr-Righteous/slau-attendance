<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $departments = Department::all();

        $courses = [
            // Computer Science Department
            [
                'name' => 'Bachelor of Science in Computer Science',
                'code' => 'BSCS',
                'department_id' => $departments->where('name', 'Computer Science')->first()->id,
                'duration_years' => 4,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Bachelor of Science in Software Engineering',
                'code' => 'BSSE',
                'department_id' => $departments->where('name', 'Computer Science')->first()->id,
                'duration_years' => 4,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],

            // Business Department
            [
                'name' => 'Bachelor of Business Administration',
                'code' => 'BBA',
                'department_id' => $departments->where('name', 'Business Administration')->first()->id,
                'duration_years' => 3,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'Bachelor of Commerce',
                'code' => 'BCOM',
                'department_id' => $departments->where('name', 'Business Administration')->first()->id,
                'duration_years' => 3,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],

            // Engineering Department
            [
                'name' => 'Bachelor of Engineering in Civil Engineering',
                'code' => 'BECE',
                'department_id' => $departments->where('name', 'Engineering')->first()->id,
                'duration_years' => 4,
                'description' => 'Covers structural design, construction management, and infrastructure development.',
            ],
            [
                'name' => 'Bachelor of Engineering in Electrical Engineering',
                'code' => 'BEEE',
                'department_id' => $departments->where('name', 'Engineering')->first()->id,
                'duration_years' => 4,
                'description' => 'Focuses on electrical systems, power generation, and electronics.',
            ],

            // Arts Department
            [
                'name' => 'Bachelor of Arts in English Literature',
                'code' => 'BAEL',
                'department_id' => $departments->where('name', 'Arts')->first()->id,
                'duration_years' => 3,
                'description' => 'Study of English literature, critical theory, and literary analysis.',
            ],
            [
                'name' => 'Bachelor of Arts in History',
                'code' => 'BAH',
                'department_id' => $departments->where('name', 'Arts')->first()->id,
                'duration_years' => 3,
                'description' => 'Explores historical events, research methods, and historical analysis.',
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        $this->command->info('Courses seeded successfully!');
    }
}