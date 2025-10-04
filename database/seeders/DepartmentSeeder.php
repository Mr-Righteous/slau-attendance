<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Computer Science',
                'code' => 'CS',
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
            ],
            [
                'name' => 'Physics',
                'code' => 'PHYS',
            ],
            [
                'name' => 'Chemistry',
                'code' => 'CHEM',
            ],
            [
                'name' => 'Biology',
                'code' => 'BIO',
            ],
            [
                'name' => 'English Literature',
                'code' => 'ENG',
            ],
            [
                'name' => 'History',
                'code' => 'HIST',
            ],
            [
                'name' => 'Business Administration',
                'code' => 'BUS',
            ],
            [
                'name' => 'Economics',
                'code' => 'ECON',
            ],
            [
                'name' => 'Mechanical Engineering',
                'code' => 'ME',
            ],
            [
                'name' => 'Electrical Engineering',
                'code' => 'EE',
            ],
            [
                'name' => 'Civil Engineering',
                'code' => 'CE',
            ],
            [
                'name' => 'Psychology',
                'code' => 'PSY',
            ],
            [
                'name' => 'Sociology',
                'code' => 'SOC',
            ],
        ];

        DB::table('departments')->insert($departments);
    }
}