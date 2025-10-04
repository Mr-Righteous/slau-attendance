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

    // Programs that include this course unit
    public function programs()
    {
        return $this->belongsToMany(
            Program::class,
            'program_course_units',
            'course_unit_id',
            'program_id'
        )
        ->withPivot(['default_year', 'default_semester', 'is_core'])
        ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_unit_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_unit_id', 'student_id')
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
