<div class="">
    <div class="">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 pb-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Manage Course Units</h2>
                <p class="text-sm text-gray-600 mt-1">Create and manage academic course units</p>
            </div>
            <button wire:click="openCreateModal" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Add New Course Unit
            </button>
        </div>

        <!-- Filters -->
        <div class="p-6 border-b">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" 
                           wire:model.live="search"
                           placeholder="Search by code or name..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select wire:model.live="departmentFilter" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select wire:model.live="semesterFilter" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Semesters</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                    <select wire:model.live="academicYearFilter" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Years</option>
                        @for($year = 2020; $year <= 2030; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <!-- Course Units Table -->
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course Unit
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lecturer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($courseUnits as $courseUnit)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $courseUnit->code }}</div>
                                    <div class="text-sm text-gray-500">{{ $courseUnit->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $courseUnit->department->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $courseUnit->department->code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($courseUnit->lecturer)
                                        <div class="text-sm text-gray-900">{{ $courseUnit->lecturer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $courseUnit->lecturer->email }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex flex-col space-y-1">
                                        <div class="flex items-center">
                                            <span class="font-medium">Credits:</span>
                                            <span class="ml-1">{{ $courseUnit->credits }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="font-medium">Semester:</span>
                                            <span class="ml-1">{{ $courseUnit->semester }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="font-medium">Year:</span>
                                            <span class="ml-1">{{ $courseUnit->academic_year }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="font-medium">Courses:</span>
                                            <span class="ml-1">{{ $courseUnit->courses->count() }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="font-medium">Sessions:</span>
                                            <span class="ml-1">{{ $courseUnit->classSessions->count() }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button wire:click="editCourseUnit({{ $courseUnit->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition text-sm">
                                            Edit
                                        </button>
                                        <button wire:click="linkToCourse({{ $courseUnit->id }})"
                                                class="text-green-600 hover:text-green-900 transition text-sm">
                                            Link Courses
                                        </button>
                                        <button wire:click="deleteCourseUnit({{ $courseUnit->id }})"
                                                wire:confirm="Are you sure you want to delete this course unit?"
                                                class="text-red-600 hover:text-red-900 transition text-sm">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No course units found. 
                                    <button wire:click="openCreateModal" class="text-blue-600 hover:text-blue-800 ml-1">
                                        Create the first course unit
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $courseUnits->links() }}
            </div>
        </div>
    </div>

    <!-- Create Course Unit Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Create New Course Unit</h3>
                    <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="createCourseUnit">
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                    Course Unit Code *
                                </label>
                                <input type="text" 
                                       wire:model="code"
                                       id="code"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="e.g., CS101">
                                @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="credits" class="block text-sm font-medium text-gray-700 mb-1">
                                    Credits *
                                </label>
                                <select wire:model="credits"
                                        id="credits"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }} Credit{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                @error('credits') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Course Unit Name *
                            </label>
                            <input type="text" 
                                   wire:model="name"
                                   id="name"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., Introduction to Programming">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea wire:model="description"
                                      id="description"
                                      rows="3"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Course unit description..."></textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Department *
                                </label>
                                <select wire:model="department_id"
                                        id="department_id"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="lecturer_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Lecturer
                                </label>
                                <select wire:model="lecturer_id"
                                        id="lecturer_id"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">No Lecturer</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                    @endforeach
                                </select>
                                @error('lecturer_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">
                                    Semester *
                                </label>
                                <select wire:model="semester"
                                        id="semester"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="1">Semester 1</option>
                                    <option value="2">Semester 2</option>
                                </select>
                                @error('semester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">
                                Academic Year *
                            </label>
                            <select wire:model="academic_year"
                                    id="academic_year"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @for($year = 2020; $year <= 2030; $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            @error('academic_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 p-6 border-t">
                        <button type="button" 
                                wire:click="closeCreateModal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Create Course Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit Course Unit Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Course Unit</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="updateCourseUnit">
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_code" class="block text-sm font-medium text-gray-700 mb-1">
                                    Course Unit Code *
                                </label>
                                <input type="text" 
                                       wire:model="code"
                                       id="edit_code"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_credits" class="block text-sm font-medium text-gray-700 mb-1">
                                    Credits *
                                </label>
                                <select wire:model="credits"
                                        id="edit_credits"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }} Credit{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                @error('credits') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Course Unit Name *
                            </label>
                            <input type="text" 
                                   wire:model="name"
                                   id="edit_name"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea wire:model="description"
                                      id="edit_description"
                                      rows="3"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="edit_department_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Department *
                                </label>
                                <select wire:model="department_id"
                                        id="edit_department_id"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_lecturer_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Lecturer
                                </label>
                                <select wire:model="lecturer_id"
                                        id="edit_lecturer_id"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">No Lecturer</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                    @endforeach
                                </select>
                                @error('lecturer_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_semester" class="block text-sm font-medium text-gray-700 mb-1">
                                    Semester *
                                </label>
                                <select wire:model="semester"
                                        id="edit_semester"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="1">Semester 1</option>
                                    <option value="2">Semester 2</option>
                                </select>
                                @error('semester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="edit_academic_year" class="block text-sm font-medium text-gray-700 mb-1">
                                Academic Year *
                            </label>
                            <select wire:model="academic_year"
                                    id="edit_academic_year"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @for($year = 2020; $year <= 2030; $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            @error('academic_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 p-6 border-t">
                        <button type="button" 
                                wire:click="closeEditModal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Update Course Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>