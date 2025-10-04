<?php

namespace App\Models;

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

    // Student's enrollments (through enrollments table)
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id', 'user_id');
    }

    // Student's courses (many-to-many through enrollments)
    public function courseUnits()
    {
        return $this->belongsToMany(CourseUnit::class, 'enrollments', 'student_id', 'course_id', 'user_id')
            ->withTimestamps();
    }

    // Student's attendance records
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id', 'user_id');
    }

    // Calculate attendance percentage for a specific course
    public function getAttendancePercentage($courseId)
    {
        $totalSessions = ClassSession::where('course_id', $courseId)->count();
        
        if ($totalSessions === 0) {
            return 0;
        }

        $attendedSessions = AttendanceRecord::where('student_id', $this->user_id)
            ->whereHas('classSession', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($attendedSessions / $totalSessions) * 100, 2);
    }

    // Add to Student model:

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Get course units student should be taking based on their program, year, semester
    public function getDefaultCourseUnits()
    {
        if (!$this->program_id || !$this->current_year || !$this->current_semester) {
            return collect([]);
        }

        return $this->program
            ->getCourseUnitsForYearSemester($this->current_year, $this->current_semester);
    }

    // Check if student is taking a retake (enrolled in unit not matching their current year/semester)
    public function isRetake($courseUnitId)
    {
        if (!$this->program_id || !$this->current_year || !$this->current_semester) {
            return false;
        }

        $programCourseUnit = ProgramCourseUnit::where('program_id', $this->program_id)
            ->where('course_unit_id', $courseUnitId)
            ->first();

        if (!$programCourseUnit) {
            return false; // Not part of their program
        }

        return $programCourseUnit->default_year != $this->current_year 
            || $programCourseUnit->default_semester != $this->current_semester;
    }

}
