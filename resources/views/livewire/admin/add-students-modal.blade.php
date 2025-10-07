<!-- resources/views/livewire/admin/add-students-modal.blade.php -->
<div>
    <!-- Trigger Button -->
    <button type="button" 
            wire:click="openModal"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
        Add Students to Session
    </button>

    <!-- Modal -->
    @if($isOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">Add Students to Class Session</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Filters -->
                <div class="p-6 border-b bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                            <select wire:model="filters.course_id" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                            <select wire:model="filters.academic_year" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}">{{ $year }}/{{ $year + 1 }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year of Study</label>
                            <select wire:model="filters.year_of_study" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">All Years</option>
                                <option value="1">Year 1</option>
                                <option value="2">Year 2</option>
                                <option value="3">Year 3</option>
                                <option value="4">Year 4</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                            <select wire:model="filters.semester" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">Both</option>
                                <option value="1">Semester 1</option>
                                <option value="2">Semester 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <button wire:click="loadEligibleStudents" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Load Students
                        </button>
                        {{-- @dd($eligibleStudents) --}}
                        <div class="text-sm text-gray-600">
                            {{ $eligibleStudents->count() }} students found
                        </div>
                    </div>
                </div>

                <!-- Students List -->
                <div class="p-6 max-h-96 overflow-y-auto">
                    <div class="space-y-3">
                        @foreach($eligibleStudents as $student)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" 
                                           wire:model="selectedStudents"
                                           value="{{ $student->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $student->name }}</div>
                                        <div class="text-sm text-gray-600">
                                            {{ $student->registration_number }} • 
                                            Year {{ $student->getCurrentAcademicProgress()->year_of_study }} Sem {{ $student->getCurrentAcademicProgress()->semester }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-sm text-gray-500">
                                    {{ $student->course->code }}
                                </div>
                            </div>
                        @endforeach

                        @if($eligibleStudents->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                No students found matching your criteria.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end space-x-3 p-6 border-t bg-gray-50">
                    <button wire:click="closeModal" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Cancel
                    </button>
                    <button wire:click="addStudentsToSession" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium disabled:opacity-50">
                        <span wire:loading.remove>Add Selected Students</span>
                        <span wire:loading>Adding...</span>
                        ({{ count($selectedStudents) }})
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>