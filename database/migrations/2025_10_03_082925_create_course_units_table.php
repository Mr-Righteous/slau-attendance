<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('semester'); // e.g., "1", "2", "Summer"
            $table->string('academic_year'); // e.g., "2024/2025"
            $table->string('description')->nullable();
            $table->integer('credits')->default(3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_units');
    }
};