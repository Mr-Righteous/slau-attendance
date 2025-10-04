<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">View Course Attendance</h1>

        <!-- Course Selection -->
        <div class="mb-6 max-w-sm">
            <label for="course_id" class="block text-sm font-medium text-gray-700">Select a Course</label>
            <select wire:model.live="selectedCourseId" id="course_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">-- Choose a course --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                @endforeach
            </select>
        </div>

        @if($selectedCourseId && count($students) > 0)
            <!-- Attendance Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border-b-2 border-gray-200 text-left font-semibold text-gray-600">Student Name</th>
                            @foreach($sessions as $session)
                                <th class="px-4 py-2 border-b-2 border-gray-200 text-center font-semibold text-gray-600">{{ $session->date->format('M d') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($students as $student)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $student->name }}</td>
                                @foreach($sessions as $session)
                                    @php
                                        $record = $attendanceData->get($student->user_id . '-' . $session->id);
                                        $status = $record ? $record->status : 'N/A';
                                        $color = match($status) {
                                            'present' => 'bg-green-100 text-green-800',
                                            'late' => 'bg-yellow-100 text-yellow-800',
                                            'absent' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <td class="px-4 py-2 text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $color }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($selectedCourseId)
            <div class="text-center py-12 text-gray-500">
                <p>No students are enrolled in this course, or attendance has not been recorded yet.</p>
            </div>
        @endif
    </div>
</div>