<div class="">
    <div class="">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Manage Courses</h2>
                <p class="text-sm text-gray-600 mt-1">Create, edit, and manage university courses</p>
            </div>
            <button wire:click="openCreateModal" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                + Create Course
            </button>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <input type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by code or name..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-4">
            </div>
            
            @hasanyrole(['big-admin','super-admin','faculty-dean'])
                <div>
                    <select wire:model.live="filterDepartment" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-4">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endhasanyrole
            
            <div>
                <select wire:model.live="filterSemester" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-4">
                    <option value="">All Semesters</option>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Code
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Duration
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Students
                        </th>
                        
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $course->code }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $course->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $course->department->code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $course->duration_years }} years
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $course->students_count }} students
                                </span>
                            </td>
                           
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="openEditModal({{ $course->id }})" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $course->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this course?')"
                                        class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No courses found. Create your first course to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $courses->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" aria-describedby="modal-description">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal" style="z-index: 50;"></div>

            <!-- Modal panel -->
            <div class="flex items-center justify-center min-h-screen px-4 py-6 sm:p-0">
                <div class="relative z-60 inline-block bg-white rounded-lg shadow-xl transform transition-all sm:max-w-2xl sm:w-full max-w-full mx-4 sm:mx-auto">
                    <div class="px-6 py-5 sm:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                                {{ $editMode ? 'Edit Course' : 'Create New Course' }}
                            </h3>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close modal">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="save">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Course Code -->
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-900 mb-2">Course Code *</label>
                                    <input type="text" 
                                        id="code"
                                        wire:model.debounce.500ms="code"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base py-2.5 px-4"
                                        placeholder="e.g., CS101">
                                    @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                
                            </div>

                            <!-- Course Name -->
                            <div class="mt-6">
                                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Course Name *</label>
                                <input type="text" 
                                    id="name"
                                    wire:model.debounce.500ms="name"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base py-2.5 px-4"
                                    placeholder="e.g., Introduction to Computer Science">
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <!-- Department -->
                                <div>
                                    <label for="department_id" class="block text-sm font-medium text-gray-900 mb-2">Department *</label>
                                    <select id="department_id"
                                            wire:model="department_id"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base py-2.5 px-4">
                                        <option value="">-- Select Department --</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Academic Year -->
                                <div>
                                    <label for="duration_years" class="block text-sm font-medium text-gray-900 mb-2">Duration (Years) *</label>
                                    <input type="number" 
                                        id="duration_years"
                                        wire:model.debounce.500ms="duration_years"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base py-2.5 px-4"
                                        placeholder="e.g., 3">
                                    @error('duration_years') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                            </div>

                            <!-- Modal Actions -->
                            <div class="mt-8 flex justify-end space-x-3">
                                <button type="button" 
                                        wire:click="closeModal"
                                        class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                    Cancel
                                </button>
                                <button type="submit"
                                        wire:loading.attr="disabled"
                                        class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                    <span wire:loading wire:target="save">Saving...</span>
                                    <span wire:loading.remove wire:target="save">
                                        {{ $editMode ? 'Update Course' : 'Create Course' }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>