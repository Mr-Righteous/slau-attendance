<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseCourseUnit extends Pivot
{
    protected $fillable = [
        'program_id',
        'course_unit_id',
        'default_year',
        'default_semester',
        'is_core',
    ];

    protected $casts = [
        'is_core' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function courseUnit()
    {
        return $this->belongsTo(CourseUnit::class);
    }
}