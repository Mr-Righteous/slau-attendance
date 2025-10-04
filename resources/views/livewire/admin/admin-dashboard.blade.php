<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-sm text-gray-600 mt-1">Welcome back! Here's what's happening today.</p>
    </div>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</div>
            <div class="text-xs text-gray-600 mt-1">Students</div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_lecturers']) }}</div>
            <div class="text-xs text-gray-600 mt-1">Lecturers</div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-purple-500">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_courses']) }}</div>
            <div class="text-xs text-gray-600 mt-1">Courses</div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-yellow-500">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_enrollments']) }}</div>
            <div class="text-xs text-gray-600 mt-1">Enrollments</div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-indigo-500">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_sessions']) }}</div>
            <div class="text-xs text-gray-600 mt-1">Sessions</div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-red-500">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_attendance_records']) }}</div>
            <div class="text-xs text-gray-600 mt-1">Records</div>
        </div>
    </div>

    <!-- Today's Activity & Attendance Rate -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-lg shadow-sm text-white">
            <h3 class="text-lg font-semibold mb-3">Today's Activity</h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-blue-100">Sessions</span>
                    <span class="text-2xl font-bold">{{ $todayStats['sessions'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-blue-100">Attendance Marked</span>
                    <span class="text-2xl font-bold">{{ $todayStats['attendance_marked'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-lg shadow-sm text-white col-span-2">
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-lg font-semibold">Attendance Rate</h3>
                <select wire:model.live="dateRange" 
                        class="text-xs bg-white/20 border-white/30 rounded px-2 py-1 text-white">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="all">All Time</option>
                </select>
            </div>
            <div class="flex items-center justify-center">
                <div class="text-6xl font-bold">{{ $attendanceRate }}%</div>
            </div>
            <div class="mt-3 text-center text-green-100 text-sm">
                {{ $attendanceRate >= 75 ? '✓ Good attendance rate' : ($attendanceRate >= 60 ? '⚠ Moderate attendance' : '⚠ Low attendance - needs attention') }}
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <a href="{{ route('admin.attendance') }}" 
               class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-center">
                <div class="text-blue-600 font-semibold text-sm">Mark Attendance</div>
            </a>
            <a href="{{ route('admin.courses') }}" 
               class="p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition text-center">
                <div class="text-purple-600 font-semibold text-sm">Manage Courses</div>
            </a>
            <a href="{{ route('admin.enrollments') }}" 
               class="p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition text-center">
                <div class="text-green-600 font-semibold text-sm">Enrollments</div>
            </a>
            <a href="{{ route('admin.view-attendance') }}" 
               class="p-4 border-2 border-gray-200 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition text-center">
                <div class="text-yellow-600 font-semibold text-sm">View Records</div>
            </a>
            <a href="{{ route('admin.import') }}" 
               class="p-4 border-2 border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition text-center">
                <div class="text-indigo-600 font-semibold text-sm">Import Data</div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Low Attendance Alerts -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Low Attendance Sessions</h3>
                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                    {{ $lowAttendanceSessions->count() }} alerts
                </span>
            </div>
            @if($lowAttendanceSessions->count() > 0)
                <div class="space-y-3">
                    @foreach($lowAttendanceSessions as $session)
                        @php
                            $total = $session->attendance_records_count;
                            $present = $session->present_count;
                            $rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                        @endphp
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $session->course->code }}</div>
                                    <div class="text-sm text-gray-600">{{ $session->date->format('M d, Y') }} • {{ $session->start_time }}</div>
                                </div>
                                <span class="px-2 py-1 bg-red-200 text-red-800 text-xs font-bold rounded">
                                    {{ $rate }}%
                                </span>
                            </div>
                            <div class="mt-2 text-xs text-gray-600">
                                {{ $present }}/{{ $total }} students attended
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 text-sm">
                    <div class="text-green-500 text-4xl mb-2">✓</div>
                    No low attendance sessions
                </div>
            @endif
        </div>

        <!-- Students with Low Attendance -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Students Needing Attention</h3>
                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                    {{ $lowAttendanceStudents->count() }} students
                </span>
            </div>
            @if($lowAttendanceStudents->count() > 0)
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($lowAttendanceStudents as $student)
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $student->name }}</div>
                                    <div class="text-xs text-gray-600">
                                        {{ $student->registration_number }} • {{ $student->department->name ?? 'N/A' }}
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs font-bold rounded
                                    {{ $student->attendance_rate >= 50 ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800' }}">
                                    {{ $student->attendance_rate }}%
                                </span>
                            </div>
                            <div class="mt-1 text-xs text-gray-600">
                                {{ AttendanceRecord::where('student_id', $student->id)->whereIn('status', ['present', 'late'])->count() }}/{{ $student->total_sessions }} sessions attended
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 text-sm">
                    <div class="text-green-500 text-4xl mb-2">✓</div>
                    All students have good attendance
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Sessions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Sessions</h3>
            @if($recentSessions->count() > 0)
                <div class="space-y-3">
                    @foreach($recentSessions as $session)
                        <div class="flex justify-between items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $session->course->code }}</div>
                                <div class="text-sm text-gray-600">{{ $session->course->name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $session->date->format('M d, Y') }} • {{ $session->start_time }}
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                                {{ $session->attendance_records_count }} records
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 text-sm">No recent sessions</div>
            @endif
        </div>

        <!-- Popular Courses -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Enrolled Courses</h3>
            @if($popularCourses->count() > 0)
                <div class="space-y-3">
                    @foreach($popularCourses as $course)
                        <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $course->code }}</div>
                                <div class="text-sm text-gray-600">{{ $course->name }}</div>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-bold rounded-full">
                                {{ $course->enrollments_count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 text-sm">No courses available</div>
            @endif
        </div>
    </div>

    <!-- Recent Enrollments -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Enrollments</h3>
        @if($recentEnrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrolled</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentEnrollments as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $enrollment->student->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $enrollment->student->registration_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $enrollment->course->code }}</div>
                                    <div class="text-xs text-gray-500">{{ $enrollment->course->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $enrollment->enrolled_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 text-sm">No recent enrollments</div>
        @endif
    </div>
</div> 