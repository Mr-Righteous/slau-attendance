<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\CourseUnit;

class CourseCourseUnitSeeder extends Seeder
{
    public function run()
    {
        $courses = Course::with('department')->get();
        $courseUnits = CourseUnit::all();

        $pivotData = [];

        foreach ($courses as $course) {
            // Get course units that belong to this course's department
            $relevantUnits = $courseUnits->where('department_id', $course->department_id);
            
            $year = 1;
            $semester = 1;
            
            foreach ($relevantUnits as $unit) {
                $pivotData[] = [
                    'course_id' => $course->id,
                    'course_unit_id' => $unit->id,
                    'default_year' => $year,
                    'default_semester' => $semester,
                    'is_core' => true, // You can modify this logic as needed
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Alternate semesters
                $semester = $semester == 1 ? 2 : 1;
                if ($semester == 1) {
                    $year++;
                    if ($year > $course->duration_years) {
                        $year = 1;
                    }
                }
            }
        }

        DB::table('course_course_units')->insert($pivotData);

        $this->command->info('Course-CourseUnit relationships seeded successfully!');
    }
}