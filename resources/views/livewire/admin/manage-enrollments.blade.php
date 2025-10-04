<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Manage Enrollments</h2>
            <p class="text-sm text-gray-600 mt-1">Enroll students in courses and manage course registrations</p>
        </div>

        <!-- Quick Enroll by Course -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="font-semibold text-blue-900 mb-3">Quick Enroll Students</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($courses->take(6) as $course)
                    <div class="p-3 bg-white rounded border border-blue-200 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $course->code }}</div>
                                <div class="text-sm text-gray-600">{{ $course->name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $course->enrollments()->count() }} students enrolled
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-2">
                            <button wire:click="openEnrollModal({{ $course->id }})"
                                    class="flex-1 px-3 py-1.5 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                Enroll
                            </button>
                            <button wire:click="openBulkEnrollModal({{ $course->id }})"
                                    class="flex-1 px-3 py-1.5 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition">
                                Bulk
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($courses->count() > 6)
                <p class="text-xs text-blue-600 mt-3">Showing 6 of {{ $courses->count() }} courses. Use filters below to find more.</p>
            @endif
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <input type="text" 
                       wire:model.live.debounce.300ms="searchEnrollment"
                       placeholder="Search student or course..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <select wire:model.live="filterCourse" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <select wire:model.live="filterDepartment" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Enrollments Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Registration No.
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Enrolled At
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $enrollment->student->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $enrollment->student->registration_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $enrollment->student->department->code ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $enrollment->course->code }}</div>
                                <div class="text-xs text-gray-500">{{ $enrollment->course->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $enrollment->enrolled_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="unenroll({{ $enrollment->id }})" 
                                        onclick="return confirm('Are you sure you want to unenroll this student?')"
                                        class="text-red-600 hover:text-red-900">
                                    Unenroll
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No enrollments found. Start by enrolling students in courses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $enrollments->links() }}
        </div>
    </div>

    <!-- Individual Enrollment Modal -->
    @if($showEnrollModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Enroll Students</h3>
                                <p class="text-sm text-gray-600">{{ $selectedCourse->code }} - {{ $selectedCourse->name }}</p>
                            </div>
                            <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Search Students -->
                        <div class="mb-4">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="searchStudent"
                                   placeholder="Search students by name, reg number, or email..."
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Students List -->
                        <div class="max-h-96 overflow-y-auto border rounded-lg">
                            @forelse($availableStudents as $student)
                                <div class="p-3 border-b hover:bg-gray-50 cursor-pointer"
                                     wire:click="toggleStudent({{ $student->id }})">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   @if(in_array($student->id, $selectedStudents)) checked @endif
                                                   class="rounded border-gray-300 text-blue-600 mr-3"
                                                   onclick="event.stopPropagation()">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $student->name }}</div>
                                                <div class="text-sm text-gray-600">
                                                    {{ $student->registration_number }} • {{ $student->department->name ?? 'No Department' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-500">
                                    @if($searchStudent)
                                        No students found matching your search.
                                    @else
                                        All students are already enrolled in this course.
                                    @endif
                                </div>
                            @endforelse
                        </div>

                        <!-- Selected Count -->
                        @if(count($selectedStudents) > 0)
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-900">{{ count($selectedStudents) }} student(s) selected</p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" 
                                    wire:click="closeModals"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Cancel
                            </button>
                            <button type="button"
                                    wire:click="enrollSelectedStudents"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Enroll Selected Students
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Enrollment Modal -->
    @if($showBulkEnrollModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Bulk Enroll Students</h3>
                                <p class="text-sm text-gray-600">{{ $selectedCourse->code }} - {{ $selectedCourse->name }}</p>
                            </div>
                            <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-sm text-yellow-800">
                                Enter registration numbers separated by commas or new lines.
                            </p>
                        </div>

                        <!-- Bulk Input -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Registration Numbers</label>
                            <textarea wire:model="bulkStudentIds"
                                      rows="10"
                                      placeholder="S2024001, S2024002, S2024003&#10;or&#10;S2024001&#10;S2024002&#10;S2024003"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"></textarea>
                            @error('bulkStudentIds') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" 
                                    wire:click="closeModals"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Cancel
                            </button>
                            <button type="button"
                                    wire:click="bulkEnrollStudents"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Enroll Students
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>