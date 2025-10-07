<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminAndRoleSeeder::class,
            // DepartmentSeeder::class,
            // UserSeeder::class,
            // CourseSeeder::class,
            // CourseUnitSeeder::class,
            // StudentSeeder::class,
            // CourseCourseUnitSeeder::class,
            // ... other seeders
        ]);
    }
}