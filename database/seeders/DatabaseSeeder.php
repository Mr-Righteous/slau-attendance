<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
           
            FacultySeeder::class,
            DepartmentSeeder::class,
            CourseSeeder::class,
            AdminAndRoleSeeder::class,
            CourseUnitSeeder::class,
            LecturersSeeder::class,       
            // StudentSeeder::class,
            // CourseCourseUnitSeeder::class,
            // ... other seeders
        ]);
    }
}