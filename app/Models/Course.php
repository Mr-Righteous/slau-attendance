<?php

namespace App\Models;

use App\Traits\HasFacultyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    use HasFacultyScope;

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
            'course_course_units',
            'course_id',
            'course_unit_id'
        )
        ->withPivot(['default_year', 'default_semester', 'is_core'])
        ->withTimestamps();
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

    public function scopeForUserRole(Builder $query, User $user)
    {
        if ($user->hasRole('dpt-hod'))
        {
            $myFacultyId = Faculty::findOrFail($user->department->faculty_id)->id;
            return $query->where('department_id', $user->department_id);
        } elseif ($user->hasRole('faculty-dean')) {
            $myFacultyId = Faculty::findOrFail($user->department->faculty_id)->id;
            return $query->inFaculty($myFacultyId);
        } else {
            return $query;
        }
    }
}
