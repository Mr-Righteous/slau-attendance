<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900">Student Dashboard</h1>

        @if($student)
            <div class="mt-4 border-t border-gray-200 pt-4">
                <p class="text-lg text-gray-700">Welcome, <span class="font-semibold">{{ $student->name }}</span>.</p>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Registration #</span>
                        <p class="font-semibold text-gray-800">{{ $student->registration_number }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Program</span>
                        <p class="font-semibold text-gray-800">{{ $student->program->name ?? 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Department</span>
                        <p class="font-semibold text-gray-800">{{ $student->program->department->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Academic Year</span>
                        <p class="font-semibold text-gray-800">{{ $student->academic_year ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex space-x-3">
                <a href="{{ route('student.courses') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    My Courses
                </a>
                <a href="{{ route('student.attendance') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    My Attendance
                </a>
            </div>
        @else
            <p class="mt-4 text-gray-600">Your student profile could not be loaded. Please contact an administrator.</p>
        @endif
    </div>
</div>