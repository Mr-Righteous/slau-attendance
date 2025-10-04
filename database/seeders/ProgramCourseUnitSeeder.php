<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\CourseUnit;
use Illuminate\Database\Seeder;

class ProgramCourseUnitSeeder extends Seeder
{
    public function run(): void
    {
        $programs = Program::with('department')->get();
        $courseUnits = CourseUnit::all();

        if ($programs->isEmpty() || $courseUnits->isEmpty()) return;

        foreach ($programs as $program) {
            // Get course units from the same department
            $departmentCourses = $courseUnits->where('department_id', $program->department_id);

            // Attach a sample of courses to the program
            $coursesToAttach = $departmentCourses->take(3);

            foreach ($coursesToAttach as $courseUnit) {
                $program->courseUnits()->syncWithoutDetaching([
                    $courseUnit->id => [
                        'default_year' => rand(1, $program->duration_years),
                        'default_semester' => rand(1, 2),
                        'is_core' => (bool)rand(0, 1),
                    ]
                ]);
            }
        }
    }
}