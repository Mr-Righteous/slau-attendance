<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseUnit;
use Illuminate\Database\Seeder;

class CourseCourseUnitSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::with('department')->get();
        $courseUnits = CourseUnit::all();

        if ($courses->isEmpty() || $courseUnits->isEmpty()) return;

        foreach ($courses as $course) {
            // Get course units from the same department
            $departmentCourses = $courseUnits->where('department_id', $course->department_id);

            // Attach a sample of courses to the program
            $coursesToAttach = $departmentCourses->take(3);

            foreach ($coursesToAttach as $courseUnit) {
                $course->courseUnits()->syncWithoutDetaching([
                    $courseUnit->id => [
                        'default_year' => rand(1, $course->duration_years),
                        'default_semester' => rand(1, 2),
                        'is_core' => (bool)rand(0, 1),
                    ]
                ]);
            }
        }
    }
}