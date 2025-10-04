<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['name' => 'Bachelor of Science in Computer Science', 'code' => 'BSC-CS', 'department_code' => 'CS', 'duration_years' => 4],
            ['name' => 'Bachelor of Information Technology', 'code' => 'BIT', 'department_code' => 'IT', 'duration_years' => 3],
            ['name' => 'Bachelor of Business Administration', 'code' => 'BBA', 'department_code' => 'BUS', 'duration_years' => 3],
            ['name' => 'Bachelor of Science in Electrical Engineering', 'code' => 'BSEE', 'department_code' => 'EE', 'duration_years' => 4],
            ['name' => 'Bachelor of Arts in Psychology', 'code' => 'BA-PSY', 'department_code' => 'PSY', 'duration_years' => 3],
        ];

        foreach ($programs as $programData) {
            $department = Department::where('code', $programData['department_code'])->first();
            if ($department) {
                Program::firstOrCreate(
                    ['code' => $programData['code']],
                    [
                        'name' => $programData['name'],
                        'department_id' => $department->id,
                        'duration_years' => $programData['duration_years'],
                    ]
                );
            }
        }
    }
}