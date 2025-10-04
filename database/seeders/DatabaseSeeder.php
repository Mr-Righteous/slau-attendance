<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminAndRoleSeeder::class, // Creates roles
            DepartmentSeeder::class,   // Creates departments
            ProgramSeeder::class,      // Creates programs, needs departments
            UserSeeder::class,         // Creates lecturers and students, needs roles, departments, and programs
            CourseSeeder::class,       // Creates course units, needs departments and lecturers
            ProgramCourseUnitSeeder::class, // Links programs and course units
            EnrollmentSeeder::class,   // Enrolls students in course units
            ClassSessionSeeder::class, // Creates class sessions for course units
        ]);
    }
}
