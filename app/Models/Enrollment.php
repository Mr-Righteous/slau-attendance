<?php

namespace App\Models;

use App\Models\ClassSession;
use App\Models\AttendanceRecord;
use App\Models\CourseCourseUnit;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'lecturer_id',
        'department_id',
        'semester',
        'academic_year',
        'credits',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class);
    }
}