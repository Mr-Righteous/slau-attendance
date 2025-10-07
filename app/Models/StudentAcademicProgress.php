<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAcademicProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'academic_year',
        'year_of_study',
        'semester',
        'status',
        'course_units'
    ];

    protected $casts = [
        'course_units' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Automatically progress student to next semester/year
    public static function progressStudent(Student $student)
    {
        $current = $student->getCurrentAcademicProgress();
        
        if (!$current) {
            return null;
        }

        $nextSemester = $current->semester + 1;
        $nextYear = $current->year_of_study;
        $nextAcademicYear = $current->academic_year;

        // If moving from semester 2 to semester 1, increment year
        if ($nextSemester > 2) {
            $nextSemester = 1;
            $nextYear++;
            $nextAcademicYear++;
        }

        // Check if student has completed the course
        $courseDuration = $student->course->duration_years;
        if ($nextYear > $courseDuration) {
            // Student has completed the course
            return null;
        }

        return self::create([
            'student_id' => $student->id,
            'course_id' => $student->course_id,
            'academic_year' => $nextAcademicYear,
            'year_of_study' => $nextYear,
            'semester' => $nextSemester,
            'status' => 'active'
        ]);
    }
}