<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Get students whose attendance is below a certain threshold.
     *
     * @param int $threshold The attendance percentage threshold (e.g., 75).
     * @param int|null $programId Optional program ID to filter by.
     * @param int|null $departmentId Optional department ID to filter by.
     * @return array
     */
    public function getAtRiskStudents(int $threshold = 75, $programId = null, $departmentId = null): array
    {
        $atRiskStudents = [];

        $studentsQuery = Student::with(['program', 'user'])
            ->when($programId, fn ($q) => $q->where('program_id', $programId))
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId));

        $students = $studentsQuery->get();

        foreach ($students as $student) {
            $enrollments = $student->user->enrollments()->with('courseUnit')->get();
            
            foreach ($enrollments as $enrollment) {
                $course = $enrollment->courseUnit;

                $totalSessions = DB::table('class_sessions')->where('course_id', $course->id)->count();

                if ($totalSessions === 0) continue;

                $attendedCount = DB::table('attendance_records')
                    ->join('class_sessions', 'attendance_records.class_session_id', '=', 'class_sessions.id')
                    ->where('class_sessions.course_id', $course->id)
                    ->where('attendance_records.student_id', $student->user_id)
                    ->whereIn('attendance_records.status', ['present', 'late'])
                    ->count();

                $percentage = round(($attendedCount / $totalSessions) * 100);

                if ($percentage < $threshold) {
                    $atRiskStudents[$student->user_id]['student'] = $student;
                    $atRiskStudents[$student->user_id]['courses'][] = [
                        'course_name' => $course->name,
                        'percentage' => $percentage,
                    ];
                }
            }
        }

        return $atRiskStudents;
    }
}
