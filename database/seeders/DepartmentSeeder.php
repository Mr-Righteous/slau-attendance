<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            [
                'name' => 'Computer Science',
                'code' => 'CS',
            ],
            [
                'name' => 'Business Administration',
                'code' => 'BUS',
            ],
            [
                'name' => 'Engineering',
                'code' => 'ENG',
            ],
            [
                'name' => 'Arts',
                'code' => 'ART',
            ],
            [
                'name' => 'Science',
                'code' => 'SCI',
            ],
            [
                'name' => 'Medicine',
                'code' => 'MED',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        $this->command->info('Departments seeded successfully!');
    }
}