<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LecturersSeeder extends Seeder
{
    public function run()
    {
        $lecturerRole = Role::firstOrCreate(['name' => 'lecturer']);

        $departments = Department::all();

        // Create Lecturers
        $lecturers = [
            [
                'name' => 'Dr. John Smith',
                'email' => 'john.smith@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'password_changed' => true,
            ],
            [
                'name' => 'Prof. Sarah Johnson',
                'email' => 'sarah.johnson@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'password_changed' => true,
            ],
            [
                'name' => 'Dr. Michael Brown',
                'email' => 'michael.brown@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'password_changed' => true,
            ],
            [
                'name' => 'Dr. Emily Davis',
                'email' => 'emily.davis@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'password_changed' => true,
            ],
            [
                'name' => 'Prof. Robert Wilson',
                'email' => 'robert.wilson@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'password_changed' => true,
            ],
        ];

        foreach ($lecturers as $lecturerData) {
            $lecturer = User::create($lecturerData);
            $lecturer->assignRole($lecturerRole);
        }

        $this->command->info('Lecturers seeded successfully with Spatie roles!');
    }
}