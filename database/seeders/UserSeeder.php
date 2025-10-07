<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $lecturerRole = Role::firstOrCreate(['name' => 'lecturer']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        $departments = Department::all();

        // Create Admin User
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@university.edu',
            'password' => Hash::make('password'),
            'department_id' => $departments->first()->id,
            'password_changed' => true,
        ]);
        $admin->assignRole($adminRole);

        // Create Lecturers
        $lecturers = [
            [
                'name' => 'Dr. John Smith',
                'email' => 'john.smith@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'CS')->first()->id,
                'password_changed' => true,
            ],
            [
                'name' => 'Prof. Sarah Johnson',
                'email' => 'sarah.johnson@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'CS')->first()->id,
                'password_changed' => true,
            ],
            [
                'name' => 'Dr. Michael Brown',
                'email' => 'michael.brown@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'BUS')->first()->id,
                'password_changed' => true,
            ],
            [
                'name' => 'Dr. Emily Davis',
                'email' => 'emily.davis@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'ENG')->first()->id,
                'password_changed' => true,
            ],
            [
                'name' => 'Prof. Robert Wilson',
                'email' => 'robert.wilson@university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'ART')->first()->id,
                'password_changed' => true,
            ],
        ];

        foreach ($lecturers as $lecturerData) {
            $lecturer = User::create($lecturerData);
            $lecturer->assignRole($lecturerRole);
        }

        // Create Students
        $students = [
            // Computer Science Students
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@student.university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'CS')->first()->id,
                'password_changed' => false,
            ],
            [
                'name' => 'Bob Miller',
                'email' => 'bob.miller@student.university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'CS')->first()->id,
                'password_changed' => false,
            ],
            [
                'name' => 'Carol White',
                'email' => 'carol.white@student.university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'CS')->first()->id,
                'password_changed' => false,
            ],

            // Business Students
            [
                'name' => 'David Green',
                'email' => 'david.green@student.university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'BUS')->first()->id,
                'password_changed' => false,
            ],
            [
                'name' => 'Eva Black',
                'email' => 'eva.black@student.university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'BUS')->first()->id,
                'password_changed' => false,
            ],

            // Engineering Students
            [
                'name' => 'Frank Taylor',
                'email' => 'frank.taylor@student.university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'ENG')->first()->id,
                'password_changed' => false,
            ],
            [
                'name' => 'Grace Lee',
                'email' => 'grace.lee@student.university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'ENG')->first()->id,
                'password_changed' => false,
            ],

            // Arts Students
            [
                'name' => 'Henry Clark',
                'email' => 'henry.clark@student.university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'ART')->first()->id,
                'password_changed' => false,
            ],
            [
                'name' => 'Ivy Martinez',
                'email' => 'ivy.martinez@student.university.edu',
                'password' => Hash::make('password'),
                'department_id' => $departments->where('code', 'ART')->first()->id,
                'password_changed' => false,
            ],
        ];

        foreach ($students as $studentData) {
            $student = User::create($studentData);
            $student->assignRole($studentRole);
        }

        $this->command->info('Users seeded successfully with Spatie roles!');
    }
}