<?php

namespace App\Models;

use App\Models\ClassSession;
use App\Models\AttendanceRecord;
use App\Models\CourseCourseUnit;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'dob',
        'registration_number',
        'department_id',
        'current_year',
        'academic_year',
        'current_semester',
        'program_id',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }



    // Student's attendance records
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id', 'user_id');
    }

    // Calculate attendance percentage for a specific course
    public function getAttendancePercentage($courseUnitId)
    {
        $totalSessions = ClassSession::where('course_unit_id', $courseUnitId)->count();
        
        if ($totalSessions === 0) {
            return 0;
        }

        $attendedSessions = AttendanceRecord::where('student_id', $this->user_id)
            ->whereHas('classSession', function ($query) use ($courseUnitId) {
                $query->where('course_unit_id', $courseUnitId);
            })
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($attendedSessions / $totalSessions) * 100, 2);
    }

    // Add to Student model:

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Get course units student should be taking based on their program, year, semester
    public function getDefaultCourseUnits()
    {
        if (!$this->course_id || !$this->current_year || !$this->current_semester) {
            return collect([]);
        }

        return $this->course
            ->getCourseUnitsForYearSemester($this->current_year, $this->current_semester);
    }
}
