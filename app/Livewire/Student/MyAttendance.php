<?php

namespace App\Livewire\Student;

use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MyAttendance extends Component
{
    public $attendanceSummary = [];

    public function mount()
    {
        $studentId = Auth::id();
        $enrollments = Enrollment::where('student_id', $studentId)->with('courseUnit')->get();

        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment->courseUnit->id;

            // Get total number of sessions for the course
            $totalSessions = DB::table('class_sessions')->where('course_id', $courseId)->count();

            if ($totalSessions === 0) {
                $this->attendanceSummary[] = [
                    'course' => $enrollment->courseUnit,
                    'percentage' => 0,
                    'total_sessions' => 0,
                    'present_count' => 0,
                    'late_count' => 0,
                    'absent_count' => 0,
                ];
                continue;
            }

            // Get attendance counts for the student in this course
            $attendanceCounts = DB::table('attendance_records')
                ->join('class_sessions', 'attendance_records.class_session_id', '=', 'class_sessions.id')
                ->where('class_sessions.course_id', $courseId)
                ->where('attendance_records.student_id', $studentId)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            $presentCount = $attendanceCounts->get('present', 0);
            $lateCount = $attendanceCounts->get('late', 0);
            $attendedCount = $presentCount + $lateCount;
            
            // Total absent is total sessions minus what's been marked as present or late
            $absentCount = $totalSessions - $attendedCount;

            $percentage = ($totalSessions > 0) ? round(($attendedCount / $totalSessions) * 100) : 0;

            $this->attendanceSummary[] = [
                'course' => $enrollment->courseUnit,
                'percentage' => $percentage,
                'total_sessions' => $totalSessions,
                'present_count' => $presentCount,
                'late_count' => $lateCount,
                'absent_count' => $absentCount,
            ];
        }
    }

    public function render()
    {
        return view('livewire.student.my-attendance');
    }
}