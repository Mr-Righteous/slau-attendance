<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">My Attendance Summary</h1>

        @if(!empty($attendanceSummary))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($attendanceSummary as $summary)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                        <h2 class="font-bold text-lg text-gray-800">{{ $summary['course']->name }}</h2>
                        <p class="text-sm text-gray-600 font-mono mb-4">{{ $summary['course']->code }}</p>

                        <!-- Percentage and Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700">Attendance</span>
                                <span class="text-lg font-bold text-blue-600">{{ $summary['percentage'] }}%</span>
                            </div>
                            @php
                                $color = 'bg-blue-600';
                                if ($summary['percentage'] < 50) $color = 'bg-red-600';
                                elseif ($summary['percentage'] < 75) $color = 'bg-yellow-500';
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ $summary['percentage'] }}%"></div>
                            </div>
                        </div>

                        <!-- Stats Breakdown -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <p class="text-2xl font-bold text-gray-800">{{ $summary['total_sessions'] }}</p>
                                <p class="text-xs text-gray-500">Total Sessions</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-green-600">{{ $summary['present_count'] }}</p>
                                <p class="text-xs text-gray-500">Present</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-yellow-600">{{ $summary['late_count'] }}</p>
                                <p class="text-xs text-gray-500">Late</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-red-600">{{ $summary['absent_count'] }}</p>
                                <p class="text-xs text-gray-500">Absent</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <p>No attendance data is available for your enrolled courses yet.</p>
            </div>
        @endif
    </div>
</div>