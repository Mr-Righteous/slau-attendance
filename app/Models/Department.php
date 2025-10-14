<?php

namespace App\Models;

use App\Models\Faculty;
use App\Traits\HasFacultyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Department extends Model
{
    use HasFactory;
    use HasFacultyScope;

    protected $fillable = [
        'faculty_id',
        'name',
        'code',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function courseUnits()
    {
        return $this->hasMany(CourseUnit::class);
    }

    public function classSessions()
    {
        if (Auth::user()->hasAnyRole(['faculty-dean','super-admin','big-admin'])) {
            return ClassSession::query();
        }
        return $this->hasManyThrough(ClassSession::class, CourseUnit::class);
    }

    
    public function attendanceRecords()
    {
        if (Auth::user()->hasAnyRole(['faculty-dean','super-admin','big-admin'])) {
            return AttendanceRecord::query();
        }
        
        return AttendanceRecord::query()
                ->join('class_sessions', 'attendance_records.class_session_id', '=', 'class_sessions.id')
                ->join('course_units', 'class_sessions.course_unit_id', '=', 'course_units.id')
                ->where('course_units.department_id', $this->id)
                ->select('attendance_records.*');
    }
}