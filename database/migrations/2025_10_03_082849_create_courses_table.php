<?php

// ============================================
// MIGRATION 1: Create courses table
// database/migrations/2025_10_04_052046_create_courses_table.php
// ============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Bachelor of Information Technology
            $table->string('code')->unique(); // e.g., BIT, BCS
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->integer('duration_years')->default(3); // 3 or 4 years
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};