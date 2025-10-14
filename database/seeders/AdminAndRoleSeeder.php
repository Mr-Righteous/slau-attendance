<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminAndRoleSeeder extends Seeder
{
    public function run()
    {
        // Seed roles
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $bigAdminRole = Role::firstOrCreate(['name' => 'big-admin']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $facultyDeanRole = Role::firstOrCreate(['name' => 'faculty-dean']);
        $dptHodRole = Role::firstOrCreate(['name' => 'dpt-hod']);
        $lecturerRole = Role::firstOrCreate(['name' => 'lecturer']);

        $bigAdminUser = User::create([
            'name' => 'Admin User',
            'purpose' => 'Admin User',
            'email' => 'admin@example.com',
            'department_id' => Department::where('code', 'DptIE')->first()->id,
            'password' => Hash::make('password'),
        ]);

        $bigAdminUser->assignRole([$adminRole->name, $bigAdminRole->name]);

        $businessDean = User::create([
            'name' => 'Dean Business',
            'purpose' => 'Dean Business',
            'email' => 'dean.business@slau.ac.ug',
            'department_id' => Department::where('code', 'DptBA')->first()->id,
            'password' => Hash::make('dean.business.in'),
        ]);

        $businessDean->assignRole([$adminRole->name, $facultyDeanRole->name]);

        $educationDean = User::create([
            'name' => 'Dean education',
            'purpose' => 'Dean Education',
            'email' => 'dean.education@slau.ac.ug',
            'department_id' => Department::where('code', 'DptFE')->first()->id,
            'password' => Hash::make('dean.education.in'),
        ]);

        $educationDean->assignRole([$adminRole->name, $facultyDeanRole->name]);

        $scienceDean = User::create([
            'name' => 'Dean science',
            'purpose' => 'Dean Science',
            'email' => 'dean.science@slau.ac.ug',
            'department_id' => Department::where('code', 'DptIE')->first()->id,
            'password' => Hash::make('dean.science.in'),
        ]);

        $scienceDean->assignRole([$adminRole->name, $facultyDeanRole->name]);

        

        $artsDean = User::create([
            'name' => 'Dean arts',
            'purpose' => 'Dean Arts',
            'email' => 'dean.arts@slau.ac.ug',
            'department_id' => Department::where('code', 'DptPSDS')->first()->id,
            'password' => Hash::make('dean.arts.in'),
        ]);

        $artsDean->assignRole([$adminRole->name, $facultyDeanRole->name]);

        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'purpose' => 'Super Admin',
            'email' => 'admin@super.com',
            'department_id' => Department::where('code', 'DptIE')->first()->id,
            'password' => Hash::make('password'),
        ]);

        $superAdminUser->assignRole([$adminRole->name, $bigAdminRole->name, $superAdminRole]);


        // Heads of Department Users
        $informaticsHod = User::create([
            'name' => 'Hod informatics',
            'purpose' => 'HOD informatics',
            'email' => 'hod.informatics@slau.ac.ug',
            'department_id' => Department::where('code', 'DptIE')->first()->id,
            'password' => Hash::make('hod.informatics.in'),
        ]);

        $informaticsHod->assignRole([$adminRole->name, $dptHodRole->name]);
    }
}
