<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with('program.courseUnits')->get();

        if ($students->isEmpty()) return;

        foreach ($students as $student) {
            // Enroll the student in a few courses from their program
            $coursesToEnroll = $student->program->courseUnits->take(4);

            foreach ($coursesToEnroll as $courseUnit) {
                Enrollment::firstOrCreate([
                    'student_id' => $student->user_id,
                    'course_id' => $courseUnit->id,
                ], [
                    'enrolled_at' => now(),
                ]);
            }
        }
    }
}