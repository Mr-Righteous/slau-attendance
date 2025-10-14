<?php

namespace App\Models;

use App\Traits\HasFacultyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;
    use HasFacultyScope;

    protected $fillable = [
        'course_unit_id',
        'lecturer_id',
        'week',
        'date',
        'start_time',
        'end_time',
        'topic',
        'venue',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class);
    }

    public function courseUnit()
    {
        return $this->belongsTo(CourseUnit::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    // Get attendance percentage for this session
    public function getAttendancePercentageAttribute()
    {
        $total = $this->attendanceRecords()->count();
        if ($total === 0) return 0;
        
        $present = $this->attendanceRecords()
            ->whereIn('status', ['present', 'late'])
            ->count();
        
        return round(($present / $total) * 100, 2);
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

    // Get total enrolled students who should have attendance
    public function getExpectedStudentsCountAttribute()
    {
        return $this->courseUnit->students()->count();
    }
}
