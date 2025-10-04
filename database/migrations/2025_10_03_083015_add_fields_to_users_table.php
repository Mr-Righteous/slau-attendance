<?php

// ============================================
// MIGRATION 1: Add role and registration to users table
// database/migrations/2024_01_01_000001_add_role_to_users_table.php
// ============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->enum('role', ['admin', 'lecturer', 'student'])->default('student');
            $table->string('registration_number')->unique()->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('password_changed')->default(false);
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('current_year')->nullable(); // 1, 2, 3, 4
            $table->integer('current_semester')->nullable(); // 1, 2
            $table->string('academic_year')->nullable(); 
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['registration_number', 'department_id', 'password_changed']);
        });
    }
};