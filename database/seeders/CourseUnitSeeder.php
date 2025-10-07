<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseUnit;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;

class CourseUnitSeeder extends Seeder
{
    public function run()
    {
        $courses = Course::all();
        $departments = Department::all();
        $lecturers = User::role('lecturer')->get();

        $courseUnits = [
            // Computer Science Course Units
            [
                'code' => 'CS101',
                'name' => 'Introduction to Programming',
                'description' => 'Fundamentals of programming using Python',
                'course_id' => $courses->where('code', 'BSCS')->first()->id,
                'lecturer_id' => $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Computer Science')->first()->id,
                'semester' => 1,
                'academic_year' => '2024',
                'credits' => 3,
            ],
            [
                'code' => 'CS102',
                'name' => 'Data Structures and Algorithms',
                'description' => 'Study of fundamental data structures and algorithm analysis',
                'course_id' => $courses->where('code', 'BSCS')->first()->id,
                'lecturer_id' => $lecturers->skip(1)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Computer Science')->first()->id,
                'semester' => 2,
                'academic_year' => '2024',
                'credits' => 4,
            ],
            [
                'code' => 'CS201',
                'name' => 'Database Management Systems',
                'description' => 'Introduction to database design and SQL',
                'course_id' => $courses->where('code', 'BSCS')->first()->id,
                'lecturer_id' => $lecturers->skip(2)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Computer Science')->first()->id,
                'semester' => 1,
                'academic_year' => '2024',
                'credits' => 3,
            ],
            [
                'code' => 'CS202',
                'name' => 'Web Development',
                'description' => 'Building dynamic web applications',
                'course_id' => $courses->where('code', 'BSCS')->first()->id,
                'lecturer_id' => $lecturers->skip(3)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Computer Science')->first()->id,
                'semester' => 2,
                'academic_year' => '2024',
                'credits' => 3,
            ],

            // Software Engineering Course Units
            [
                'code' => 'SE101',
                'name' => 'Software Engineering Principles',
                'description' => 'Introduction to software development methodologies',
                'course_id' => $courses->where('code', 'BSSE')->first()->id,
                'lecturer_id' => $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Computer Science')->first()->id,
                'semester' => 1,
                'academic_year' => '2024',
                'credits' => 3,
            ],
            [
                'code' => 'SE102',
                'name' => 'Requirements Engineering',
                'description' => 'Gathering and analyzing software requirements',
                'course_id' => $courses->where('code', 'BSSE')->first()->id,
                'lecturer_id' => $lecturers->skip(1)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Computer Science')->first()->id,
                'semester' => 2,
                'academic_year' => '2024',
                'credits' => 3,
            ],

            // Business Administration Course Units
            [
                'code' => 'BA101',
                'name' => 'Principles of Management',
                'description' => 'Fundamental concepts of business management',
                'course_id' => $courses->where('code', 'BBA')->first()->id,
                'lecturer_id' => $lecturers->skip(4)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Business Administration')->first()->id,
                'semester' => 1,
                'academic_year' => '2024',
                'credits' => 3,
            ],
            [
                'code' => 'BA102',
                'name' => 'Marketing Fundamentals',
                'description' => 'Introduction to marketing principles and strategies',
                'course_id' => $courses->where('code', 'BBA')->first()->id,
                'lecturer_id' => $lecturers->skip(5)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Business Administration')->first()->id,
                'semester' => 2,
                'academic_year' => '2024',
                'credits' => 3,
            ],

            // Engineering Course Units
            [
                'code' => 'CE101',
                'name' => 'Engineering Mechanics',
                'description' => 'Fundamental principles of mechanics in engineering',
                'course_id' => $courses->where('code', 'BECE')->first()->id,
                'lecturer_id' => $lecturers->skip(6)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Engineering')->first()->id,
                'semester' => 1,
                'academic_year' => '2024',
                'credits' => 4,
            ],
            [
                'code' => 'EE101',
                'name' => 'Circuit Analysis',
                'description' => 'Analysis of electrical circuits and networks',
                'course_id' => $courses->where('code', 'BEEE')->first()->id,
                'lecturer_id' => $lecturers->skip(7)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Engineering')->first()->id,
                'semester' => 1,
                'academic_year' => '2024',
                'credits' => 4,
            ],

            // Arts Course Units
            [
                'code' => 'EL101',
                'name' => 'Introduction to Literature',
                'description' => 'Study of literary forms and critical approaches',
                'course_id' => $courses->where('code', 'BAEL')->first()->id,
                'lecturer_id' => $lecturers->skip(8)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Arts')->first()->id,
                'semester' => 1,
                'academic_year' => '2024',
                'credits' => 3,
            ],
            [
                'code' => 'HS101',
                'name' => 'World History',
                'description' => 'Survey of world civilizations and historical developments',
                'course_id' => $courses->where('code', 'BAH')->first()->id,
                'lecturer_id' => $lecturers->skip(9)->first()->id ?? $lecturers->first()->id,
                'department_id' => $departments->where('name', 'Arts')->first()->id,
                'semester' => 1,
                'academic_year' => '2024',
                'credits' => 3,
            ],
        ];

        foreach ($courseUnits as $courseUnit) {
            CourseUnit::create($courseUnit);
        }

        $this->command->info('Course units seeded successfully!');
    }
}