<?php

// ============================================
// COMPONENT: AdminDashboard.php
// app/Livewire/Admin/AdminDashboard.php
// ============================================

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Course;
use App\Models\ClassSession;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public $dateRange = 'today'; // today, week, month, all

    public function render()
    {
        // Count statistics
        $stats = [
            'total_students' => User::whereHas('roles', function ($q) {
                $q->where('name', 'student');
            })->count(),
            'total_lecturers' => User::whereHas('roles', function ($q) {
                $q->where('name', 'lecturer');
            })->count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'total_sessions' => ClassSession::count(),
            'total_attendance_records' => AttendanceRecord::count(),
        ];

        // Today's statistics
        $today = now()->toDateString();
        $todayStats = [
            'sessions' => ClassSession::whereDate('date', $today)->count(),
            'attendance_marked' => AttendanceRecord::whereHas('classSession', function ($q) use ($today) {
                $q->whereDate('date', $today);
            })->count(),
        ];

        // Attendance rate calculation based on date range
        $attendanceQuery = AttendanceRecord::query();
        $sessionsQuery = ClassSession::query();

        switch ($this->dateRange) {
            case 'today':
                $attendanceQuery->whereHas('classSession', function ($q) {
                    $q->whereDate('date', now()->toDateString());
                });
                $sessionsQuery->whereDate('date', now()->toDateString());
                break;
            case 'week':
                $attendanceQuery->whereHas('classSession', function ($q) {
                    $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                });
                $sessionsQuery->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $attendanceQuery->whereHas('classSession', function ($q) {
                    $q->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                });
                $sessionsQuery->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
        }

        $totalAttendance = $attendanceQuery->count();
        $presentCount = (clone $attendanceQuery)->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : 0;

        // Recent activity
        $recentSessions = ClassSession::with(['course'])
            ->withCount('attendanceRecords')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Low attendance alerts (sessions with < 60% attendance)
        $lowAttendanceSessions = ClassSession::with(['course'])
            ->withCount([
                'attendanceRecords',
                'attendanceRecords as present_count' => function ($q) {
                    $q->whereIn('status', ['present', 'late']);
                }
            ])
            ->whereHas('attendanceRecords')
            ->get()
            ->filter(function ($session) {
                $total = $session->attendance_records_count;
                $present = $session->present_count;
                $rate = $total > 0 ? ($present / $total) * 100 : 0;
                return $rate < 60;
            })
            ->take(5);

        // Students with low attendance (< 75%)
        $lowAttendanceStudents = User::whereHas('roles', function ($q) {
            $q->where('name', 'student');
        })
        ->with('department')
        ->get()
        ->map(function ($student) {
            $total = AttendanceRecord::where('student_id', $student->id)->count();
            $present = AttendanceRecord::where('student_id', $student->id)
                ->whereIn('status', ['present', 'late'])
                ->count();
            
            $student->total_sessions = $total;
            $student->attendance_rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;
            
            return $student;
        })
        ->filter(function ($student) {
            return $student->total_sessions > 0 && $student->attendance_rate < 75;
        })
        ->sortBy('attendance_rate')
        ->take(10);

        // Courses by enrollment count
        $popularCourses = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(5)
            ->get();

        // Recent enrollments
        $recentEnrollments = Enrollment::with(['student', 'course'])
            ->orderBy('enrolled_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'todayStats' => $todayStats,
            'attendanceRate' => $attendanceRate,
            'recentSessions' => $recentSessions,
            'lowAttendanceSessions' => $lowAttendanceSessions,
            'lowAttendanceStudents' => $lowAttendanceStudents,
            'popularCourses' => $popularCourses,
            'recentEnrollments' => $recentEnrollments,
        ]);
    }
}
