<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUnit extends Model
{
    use HasFactory;

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



    public function classSessions()
    {
        return $this->hasMany(ClassSession::class, 'course_unit_id');
    }

    public function getTotalSessionsAttribute()
    {
        return $this->classSessions()->count();
    }
}
