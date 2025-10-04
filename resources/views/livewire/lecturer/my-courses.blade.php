<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">My Courses</h1>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Course Name</th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Enrolled Students</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($courses as $course)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $course->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $course->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $course->department->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $course->enrollments_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">You are not assigned to any courses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>