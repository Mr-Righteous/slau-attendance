<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'department_id',
        'duration_years',
        'description',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Course units that belong to this program
    public function courseUnits()
    {
        return $this->belongsToMany(
            CourseUnit::class,
            'program_course_units',
            'program_id',
            'course_unit_id'
        )
        ->withPivot(['default_year', 'default_semester', 'is_core'])
        ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Get course units for specific year and semester
    public function getCourseUnitsForYearSemester($year, $semester)
    {
        return $this->courseUnits()
            ->wherePivot('default_year', $year)
            ->wherePivot('default_semester', $semester)
            ->get();
    }

    // Get core course units
    public function getCoreCourseUnits()
    {
        return $this->courseUnits()
            ->wherePivot('is_core', true)
            ->get();
    }
}
