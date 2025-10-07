<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ClassSession;
use App\Models\AttendanceRecord;
use App\Models\CourseUnit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AttendanceService
{
    /**
     * Record attendance for a class session
     *
     * @param int $sessionId
     * @param array $attendanceData [student_id => status]
     * @param int $markedBy
     * @return void
     */
    public function recordAttendance(int $sessionId, array $attendanceData, int $markedBy): void
    {
        $session = ClassSession::findOrFail($sessionId);
        $now = now();
        
        $records = [];
        foreach ($attendanceData as $studentId => $status) {
            $records[] = [
                'class_session_id' => $sessionId,
                'student_id' => $studentId,
                'status' => $status,
                'marked_by' => $markedBy,
                'marked_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Delete existing records for this session to avoid duplicates
        AttendanceRecord::where('class_session_id', $sessionId)->delete();
        
        // Insert new records
        AttendanceRecord::insert($records);
    }

    /**
     * Get attendance summary for a student
     *
     * @param int $studentId
     * @param int|null $courseUnitId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getStudentAttendance(int $studentId, ?int $courseUnitId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = AttendanceRecord::with(['classSession.courseUnit', 'markedBy'])
            ->where('student_id', $studentId);
            
        if ($courseUnitId) {
            $query->whereHas('classSession', function($q) use ($courseUnitId) {
                $q->where('course_unit_id', $courseUnitId);
            });
        }
        
        if ($startDate) {
            $query->whereHas('classSession', function($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            });
        }
        
        if ($endDate) {
            $query->whereHas('classSession', function($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            });
        }
        
        $records = $query->get();
        
        // Group by course unit
        $grouped = $records->groupBy(function($record) {
            return $record->classSession->courseUnit->name;
        });
        
        $summary = [];
        foreach ($grouped as $courseUnit => $attendance) {
            $totalSessions = ClassSession::where('course_unit_id', $attendance->first()->classSession->course_unit_id)->count();
            $presentCount = $attendance->whereIn('status', ['present', 'late'])->count();
            $percentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 2) : 0;
            
            $summary[] = [
                'course_unit' => $courseUnit,
                'total_sessions' => $totalSessions,
                'attended' => $presentCount,
                'absent' => $totalSessions - $presentCount,
                'percentage' => $percentage,
                'status' => $this->getAttendanceStatus($percentage)
            ];
        }
        
        return $summary;
    }
    
    /**
     * Get attendance statistics for a class session
     *
     * @param int $sessionId
     * @return array
     */
    public function getSessionAttendanceStats(int $sessionId): array
    {
        $session = ClassSession::with(['attendanceRecords', 'courseUnit'])->findOrFail($sessionId);
        $totalStudents = $session->courseUnit->students()->count();
        
        $present = $session->attendanceRecords->whereIn('status', ['present', 'late'])->count();
        $absent = $session->attendanceRecords->where('status', 'absent')->count();
        $late = $session->attendanceRecords->where('status', 'late')->count();
        $excused = $session->attendanceRecords->where('status', 'excused')->count();
        
        return [
            'total_students' => $totalStudents,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'attendance_rate' => $totalStudents > 0 ? round(($present / $totalStudents) * 100, 2) : 0,
            'absenteeism_rate' => $totalStudents > 0 ? round(($absent / $totalStudents) * 100, 2) : 0,
        ];
    }
    
    /**
     * Get students at risk of failing due to low attendance
     *
     * @param int $threshold
     * @param int|null $courseId
     * @param int|null $departmentId
     * @return Collection
     */
    public function getAtRiskStudents(int $threshold = 75, ?int $courseId = null, ?int $departmentId = null): Collection
    {
        $query = Student::with(['user', 'course', 'department']);
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        
        if ($courseId) {
            $query->where('course_id', $courseId);
        }
        
        $students = $query->get();
        
        return $students->map(function($student) use ($threshold) {
            $courseUnits = $student->getDefaultCourseUnits();
            
            $atRiskCourses = [];
            
            foreach ($courseUnits as $courseUnit) {
                $percentage = $student->getAttendancePercentage($courseUnit->id);
                
                if ($percentage < $threshold) {
                    $atRiskCourses[] = [
                        'course_name' => $courseUnit->name, // Changed key to match view
                        'percentage' => $percentage, // Changed key to match view
                        'status' => $this->getAttendanceStatus($percentage)
                    ];
                }
            }
            
            if (!empty($atRiskCourses)) {
                return [
                    'student' => $student, // Changed to pass entire student object
                    'courses' => $atRiskCourses // Changed key to match view
                ];
            }
            
            return null;
        })->filter()->values();
    }
    
    /**
     * Get attendance trends for a student or course unit
     *
     * @param int|null $studentId
     * @param int|null $courseUnitId
     * @param string $period (day, week, month)
     * @param int $limit
     * @return array
     */
    public function getAttendanceTrends(?int $studentId = null, ?int $courseUnitId = null, string $period = 'week', int $limit = 12): array
    {
        $query = AttendanceRecord::query()
            ->select(
                DB::raw('DATE(class_sessions.date) as date'),
                DB::raw('COUNT(CASE WHEN attendance_records.status IN ("present", "late") THEN 1 END) as present_count'),
                DB::raw('COUNT(*) as total')
            )
            ->join('class_sessions', 'attendance_records.class_session_id', '=', 'class_sessions.id')
            ->groupBy('date')
            ->orderBy('date')
            ->limit($limit);
            
        if ($studentId) {
            $query->where('attendance_records.student_id', $studentId);
        }
        
        if ($courseUnitId) {
            $query->where('class_sessions.course_unit_id', $courseUnitId);
        }
        
        $endDate = now();
        $startDate = clone $endDate;
        
        switch ($period) {
            case 'day':
                $startDate->subDays($limit - 1);
                $query->whereBetween('class_sessions.date', [$startDate, $endDate]);
                $format = 'Y-m-d';
                break;
                
            case 'week':
                $startDate->subWeeks($limit - 1);
                $query->whereBetween('class_sessions.date', [$startDate, $endDate])
                      ->groupBy(DB::raw('YEARWEEK(class_sessions.date)'));
                $format = 'Y-W';
                break;
                
            case 'month':
                $startDate->subMonths($limit - 1);
                $query->whereBetween('class_sessions.date', [$startDate, $endDate])
                      ->groupBy(DB::raw('YEAR(class_sessions.date)'), DB::raw('MONTH(class_sessions.date)'));
                $format = 'Y-m';
                break;
        }
        
        $results = $query->get()
            ->map(function($item) use ($format) {
                return [
                    'date' => $item->date,
                    'present_count' => (int)$item->present_count,
                    'total' => (int)$item->total,
                    'percentage' => $item->total > 0 ? round(($item->present_count / $item->total) * 100, 2) : 0
                ];
            });
            
        return [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'data' => $results
        ];
    }
    
    /**
     * Get attendance status based on percentage
     *
     * @param float $percentage
     * @return string
     */
    protected function getAttendanceStatus(float $percentage): string
    {
        if ($percentage >= 80) return 'good';
        if ($percentage >= 60) return 'warning';
        return 'critical';
    }
    
    /**
     * Get attendance summary for a lecturer
     *
     * @param int $lecturerId
     * @return array
     */
    public function getLecturerAttendanceSummary(int $lecturerId): array
    {
        $sessions = ClassSession::where('lecturer_id', $lecturerId)
            ->withCount(['attendanceRecords as present_count' => function($query) {
                $query->whereIn('status', ['present', 'late']);
            }])
            ->withCount('attendanceRecords as total_records')
            ->get();
            
        $totalSessions = $sessions->count();
        $totalPresent = $sessions->sum('present_count');
        $totalRecords = $sessions->sum('total_records');
        
        $attendanceRate = $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100, 2) : 0;
        
        return [
            'total_sessions' => $totalSessions,
            'total_present' => $totalPresent,
            'total_absent' => $totalRecords - $totalPresent,
            'attendance_rate' => $attendanceRate,
            'status' => $this->getAttendanceStatus($attendanceRate)
        ];
    }
}
