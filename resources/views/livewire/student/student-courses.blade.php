<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">My Courses</h1>

        @if($enrollments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($enrollments as $enrollment)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h2 class="font-bold text-lg text-gray-800">{{ $enrollment->courseUnit->name }}</h2>
                        <p class="text-sm text-gray-600 font-mono">{{ $enrollment->courseUnit->code }}</p>
                        
                        <div class="mt-3 text-sm space-y-2">
                            <div>
                                <span class="text-gray-500">Lecturer:</span>
                                <p class="font-medium text-gray-700">{{ $enrollment->courseUnit->lecturer->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Department:</span>
                                <p class="font-medium text-gray-700">{{ $enrollment->courseUnit->department->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <p>You are not currently enrolled in any courses.</p>
            </div>
        @endif
    </div>
</div>