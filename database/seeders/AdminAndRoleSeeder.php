<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminAndRoleSeeder extends Seeder
{
    public function run()
    {
        // Seed roles
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $lecturerRole = Role::firstOrCreate(['name' => 'lecturer']);

        // Seed admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Assign admin role to admin user
        $adminUser->assignRole($adminRole->name);
        
    }
}
