<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'date',
        'start_time',
        'end_time',
        'topic',
        'venue',
    ];

    protected $casts = [
        'date' => 'date',
    ];

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

    // Get total enrolled students who should have attendance
    public function getExpectedStudentsCountAttribute()
    {
        return $this->courseUnit->students()->count();
    }
}
