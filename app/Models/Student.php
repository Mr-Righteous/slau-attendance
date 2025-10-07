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
        'course_id',
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

    public function academicProgress()
    {
        return $this->hasMany(StudentAcademicProgress::class);
    }

    public function getCurrentAcademicProgress()
    {
        return $this->academicProgress()
            ->orderBy('academic_year', 'desc')
            ->orderBy('year_of_study', 'desc')
            ->orderBy('semester', 'desc')
            ->first();
    }

    public function getCourseUnitsForCurrentSemester()
    {
        $progress = $this->getCurrentAcademicProgress();
        
        if (!$progress) {
            return collect();
        }

        // Get course units based on student's current year/semester
        return $this->course->courseUnits()
            ->wherePivot('default_year', $progress->year_of_study)
            ->wherePivot('default_semester', $progress->semester)
            ->get();
    }

    // Get students eligible for a class session (including retakes)
    public static function getEligibleStudentsForSession(ClassSession $session, $filters = [])
    {
        $query = self::with(['user', 'course', 'academicProgress'])
            ->whereHas('academicProgress', function ($q) use ($session, $filters) {
                $q->where('status', 'active');
                
                // Filter by academic year if provided
                if (isset($filters['academic_year'])) {
                    $q->where('academic_year', $filters['academic_year']);
                }
                
                // Filter by year of study if provided
                if (isset($filters['year_of_study'])) {
                    $q->where('year_of_study', $filters['year_of_study']);
                }
                
                // Filter by semester if provided
                if (isset($filters['semester'])) {
                    $q->where('semester', $filters['semester']);
                }
            });

        // Filter by course if provided
        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        return $query->get()->filter(function ($student) use ($session) {
            // Check if student should be taking this course unit in their current progression
            $progress = $student->getCurrentAcademicProgress();
            $courseUnit = $session->courseUnit;
            
            return $courseUnit->courses()
                ->where('courses.id', $student->course_id)
                ->wherePivot('default_year', $progress->year_of_study)
                ->wherePivot('default_semester', $progress->semester)
                ->exists();
        });
    }
}
