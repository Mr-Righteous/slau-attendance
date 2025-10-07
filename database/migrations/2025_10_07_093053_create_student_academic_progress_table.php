<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_academic_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('academic_year'); // e.g., 2023, 2024
            $table->integer('year_of_study'); // 1, 2, 3, 4
            $table->integer('semester'); // 1, 2
            $table->string('status')->default('active'); // active, retake, deferred, completed
            $table->json('course_units')->nullable(); // Course units for this specific year/semester
            $table->timestamps();

            // Shorter unique constraint name
            $table->unique(['student_id', 'academic_year', 'year_of_study', 'semester'], 'stu_acad_progress_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_academic_progress');
    }
};