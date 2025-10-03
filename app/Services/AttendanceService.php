<?php   

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Models\AttendanceRecord;

class AttendanceService
{
    // Calculate attendance percentage for a student in a course
    public function getStudentAttendancePercentage(User $student, Course $course): float
    {
        $totalSessions = $course->classSessions()->count();
        
        if ($totalSessions === 0) {
            // "xig uwzm uwvmg"
            return 0;
        }   

        $attendedSessions = AttendanceRecord::where('student_id', $student->id)
            ->whereHas('classSession', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($attendedSessions / $totalSessions) * 100, 2);
    }

    // Get attendance status based on percentage
    public function getAttendanceStatus(float $percentage): string
    {
        if ($percentage >= 75) {
            return 'good';
        } elseif ($percentage >= 50) {
            return 'warning';
        } else {
            return 'critical';
        }
    }

    // Calculate average attendance for a lecturer across all courses
    public function getLecturerAverageAttendance(User $lecturer): float
    {
        $courses = $lecturer->coursesTeaching;
        
        if ($courses->isEmpty()) {
            return 0;
        }

        $totalPercentage = 0;
        $courseCount = 0;

        foreach ($courses as $course) {
            $sessions = $course->classSessions;
            if ($sessions->isNotEmpty()) {
                $totalPercentage += collect($sessions)->avg('attendance_percentage');
                $courseCount++;
            }
        }

        return $courseCount > 0 ? round($totalPercentage / $courseCount, 2) : 0;
    }
}