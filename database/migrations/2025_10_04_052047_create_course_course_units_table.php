<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_course_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_unit_id')->constrained()->cascadeOnDelete();
            $table->integer('default_year'); // 1, 2, 3, 4
            $table->integer('default_semester'); // 1, 2
            $table->boolean('is_core')->default(true); // true = required, false = elective
            $table->timestamps();

            // A course unit can only be added once per program (unique combination)
            $table->unique(['course_id', 'course_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_course_units');
    }
};
