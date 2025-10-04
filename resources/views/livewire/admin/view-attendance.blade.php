<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">View Attendance Records</h2>
                <p class="text-sm text-gray-600 mt-1">Browse and analyze attendance data</p>
            </div>
            <button wire:click="exportData" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                Export Data
            </button>
        </div>

        <!-- Stats Cards -->
        @if($showStats)
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="text-2xl font-bold text-blue-700">{{ number_format($stats['total']) }}</div>
                    <div class="text-xs text-blue-600">Total Records</div>
                </div>
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="text-2xl font-bold text-green-700">{{ number_format($stats['present']) }}</div>
                    <div class="text-xs text-green-600">Present</div>
                </div>
                <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                    <div class="text-2xl font-bold text-red-700">{{ number_format($stats['absent']) }}</div>
                    <div class="text-xs text-red-600">Absent</div>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="text-2xl font-bold text-yellow-700">{{ number_format($stats['late']) }}</div>
                    <div class="text-xs text-yellow-600">Late</div>
                </div>
                <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="text-2xl font-bold text-purple-700">{{ number_format($stats['excused']) }}</div>
                    <div class="text-xs text-purple-600">Excused</div>
                </div>
                <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                    <div class="text-2xl font-bold text-indigo-700">{{ $stats['attendance_rate'] }}%</div>
                    <div class="text-xs text-indigo-600">Attendance Rate</div>
                </div>
            </div>
        @endif

        <!-- View Mode Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="$set('viewMode', 'records')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $viewMode === 'records' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Individual Records
                </button>
                <button wire:click="$set('viewMode', 'sessions')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $viewMode === 'sessions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    By Session
                </button>
                <button wire:click="$set('viewMode', 'students')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $viewMode === 'students' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    By Student
                </button>
            </nav>
        </div>

        <!-- Filters -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Course</label>
                    <select wire:model.live="filterCourse" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($viewMode === 'records' && $filterCourse)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Session</label>
                        <select wire:model.live="filterSession" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">All Sessions</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}">
                                    {{ $session->date->format('M d, Y') }} - {{ $session->start_time }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($viewMode === 'records')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="filterStatus" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">All Statuses</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="excused">Excused</option>
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                    <select wire:model.live="filterDepartment" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($viewMode !== 'sessions')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search Student</label>
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchStudent"
                               placeholder="Name or registration number..."
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" 
                           wire:model.live="dateFrom"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" 
                           wire:model.live="dateTo"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="mt-3">
                <button wire:click="clearFilters" 
                        class="px-3 py-1.5 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                    Clear All Filters
                </button>
            </div>
        </div>

        <!-- Data Display -->
        <div class="overflow-x-auto">
            @if($viewMode === 'records')
                <!-- Individual Records View -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reg No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marked By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($data as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $record->student->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $record->student->registration_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $record->classSession->course->code }}</div>
                                    <div class="text-xs text-gray-500">{{ $record->classSession->course->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $record->classSession->date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $record->classSession->start_time }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $record->status === 'present' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $record->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $record->status === 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $record->status === 'excused' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $record->markedBy->name }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No attendance records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($viewMode === 'sessions')
                <!-- By Session View -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Topic</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Present</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Late</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Absent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($data as $session)
                            @php
                                $total = $session->attendance_records_count;
                                $attended = $session->present_count + $session->late_count;
                                $rate = $total > 0 ? round(($attended / $total) * 100, 2) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-medium text-gray-900">{{ $session->course->code }}</div>
                                    <div class="text-xs text-gray-500">{{ $session->course->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div>{{ $session->date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $session->start_time }} - {{ $session->end_time }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $session->topic ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="text-green-600 font-medium">{{ $session->present_count }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="text-yellow-600 font-medium">{{ $session->late_count }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="text-red-600 font-medium">{{ $session->absent_count }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $rate >= 75 ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $rate >= 50 && $rate < 75 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $rate < 50 ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $rate }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No sessions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($viewMode === 'students')
                <!-- By Student View -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reg No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sessions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attended</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($data as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $student->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $student->registration_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $student->department->code ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $student->total_sessions }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $student->attended_sessions }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    {{ $student->attendance_rate }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $student->attendance_rate >= 75 ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $student->attendance_rate >= 50 && $student->attendance_rate < 75 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $student->attendance_rate < 50 ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $student->attendance_rate >= 75 ? 'Good' : ($student->attendance_rate >= 50 ? 'Warning' : 'Critical') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No students found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $data->links() }}
        </div>
    </div>
</div>