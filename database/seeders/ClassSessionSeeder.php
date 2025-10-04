<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\CourseUnit;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ClassSessionSeeder extends Seeder
{
    public function run(): void
    {
        $courseUnits = CourseUnit::all();

        if ($courseUnits->isEmpty()) return;

        foreach ($courseUnits as $courseUnit) {
            // Create 5 past sessions for each course unit
            for ($i = 5; $i > 0; $i--) {
                ClassSession::create([
                    'course_id' => $courseUnit->id,
                    'lecturer_id' => $courseUnit->lecturer_id,
                    'date' => Carbon::now()->subWeeks($i),
                    'start_time' => '09:00',
                    'end_time' => '11:00',
                    'topic' => 'Topic for Week ' . (6 - $i),
                    'venue' => 'Room ' . rand(101, 305),
                ]);
            }
        }
    }
}