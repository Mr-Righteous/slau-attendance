<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $lecturerRole = Role::where('name', 'lecturer')->first();
        $studentRole = Role::where('name', 'student')->first();

        // Create Lecturers
        $departments = Department::all();
        if ($departments->isEmpty()) return;

        User::factory(10)->create()->each(function ($user) use ($lecturerRole, $departments) {
            $user->department_id = $departments->random()->id;
            $user->save();
            $user->assignRole($lecturerRole);
        });

        // Create Students
        $courses = Course::all();
        if ($courses->isEmpty()) return;

        User::factory(50)->create()->each(function ($user) use ($studentRole, $courses) {
            $course = $courses->random();
            $user->department_id = $course->department_id;
            $user->save();
            $user->assignRole($studentRole);

            Student::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'registration_number' => $user->registration_number,
                'course_id' => $course->id,
                'department_id' => $course->department_id,
                'current_year' => rand(1, $course->duration_years),
                'current_semester' => rand(1, 2),
                'academic_year' => '2024/2025',
            ]);
        });
    }
}