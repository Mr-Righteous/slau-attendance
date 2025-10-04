<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Advanced Reports: At-Risk Students</h1>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 p-4 border rounded-lg">
            <div>
                <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                <select wire:model.live="selectedDepartmentId" id="department" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="program" class="block text-sm font-medium text-gray-700">Program</label>
                <select wire:model.live="selectedProgramId" id="program" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Programs</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="threshold" class="block text-sm font-medium text-gray-700">Attendance Below: <span class="font-bold">{{ $threshold }}%</span></label>
                <input wire:model.live="threshold" type="range" id="threshold" min="1" max="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer mt-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
                <button wire:click="runReport" class="w-full mt-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Run Report
                </button>
            </div>
        </div>

        <!-- Results -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Program</th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Courses Below Threshold</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($atRiskStudents as $data)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-semibold">{{ $data['student']->name }}</div>
                                <div class="text-xs text-gray-500">{{ $data['student']->registration_number }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $data['student']->program->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <ul class="space-y-1">
                                    @foreach($data['courses'] as $courseData)
                                        <li class="flex justify-between">
                                            <span>{{ $courseData['course_name'] }}</span>
                                            <span class="font-semibold text-red-600">{{ $courseData['percentage'] }}%</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">No at-risk students found matching your criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>