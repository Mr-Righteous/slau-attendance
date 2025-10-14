<?php

namespace App\Models;

use App\Traits\HasFacultyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUnit extends Model
{
    use HasFactory;
    use HasFacultyScope;

    protected $fillable = [
        'code',
        'name',
        'description',
        'course_id',
        'lecturer_id',
        'department_id',
        'semester',
        'academic_year',
        'credits',
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Courses that include this course unit
    public function courses()
    {
        return $this->belongsToMany(
            Course::class,
            'course_course_units',
            'course_unit_id',
            'course_id'
        )
        ->withPivot(['default_year', 'default_semester', 'is_core'])
        ->withTimestamps();
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

    

    public function getDefaultYearAttribute()
    {
        return $this->courses->first()?->pivot->default_year;
    }

    public function getDefaultSemesterAttribute()
    {
        return $this->courses->first()?->pivot->default_semester;
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class, 'course_unit_id');
    }

    public function getTotalSessionsAttribute()
    {
        return $this->classSessions()->count();
    }
}
