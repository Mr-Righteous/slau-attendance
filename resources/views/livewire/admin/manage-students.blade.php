<div class="">
    <div class="">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 pb-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Manage Students</h2>
                <p class="text-sm text-gray-600 mt-1">Create and manage student accounts and information</p>
            </div>
            <button wire:click="openCreateModal" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Add New Student
            </button>
        </div>

        <!-- Filters -->
        <div class="p-6 border-b">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" 
                           wire:model.live="search"
                           placeholder="Name, Reg No, Email..."
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                    <select wire:model.live="courseFilter" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select wire:model.live="yearFilter" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Years</option>
                        @for($year = 1; $year <= 6; $year++)
                            <option value="{{ $year }}">Year {{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select wire:model.live="semesterFilter" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Both</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="">
            <div class="">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Academic Info
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-medium text-sm">
                                                {{ substr($student->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $student->registration_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $student->course->code }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->course->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Year {{ $student->current_year }} Sem {{ $student->current_semester }}
                                    </div>
                                    {{-- <div>
                                        {{ $student->department->name }}
                                    </div> --}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $student->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->phone ?? 'No phone' }}</div>
                                    <div class="text-xs text-gray-500 capitalize">{{ $student->gender }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1 text-xs">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                                            Active
                                        </span>
                                        <div class="text-gray-500">
                                            {{ $student->academic_year }} Academic Year
                                        </div>
                                        <div class="text-gray-500">
                                            {{ $student->academicProgress->count() }} Progress Records
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    {{-- <div class="flex space-x-2"> --}}
                                        <div>
                                            <button wire:click="editStudent({{ $student->id }})"
                                                    class="text-blue-600 hover:text-blue-900 transition text-sm">
                                                Edit
                                            </button>
                                        </div>
                                        <div>
                                            <button wire:click="resetPassword({{ $student->id }})"
                                                    wire:confirm="Reset password to registration number?"
                                                    class="text-orange-600 hover:text-orange-900 transition text-sm">
                                                Reset Password
                                            </button>
                                        </div>
                                        <div>
                                            <button wire:click="deleteStudent({{ $student->id }})"
                                                    wire:confirm="Are you sure you want to delete this student?"
                                                    class="text-red-600 hover:text-red-900 transition text-sm">
                                                Delete
                                            </button>
                                        </div>
                                    {{-- </div> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No students found. 
                                    <button wire:click="openCreateModal" class="text-blue-600 hover:text-blue-800 ml-1">
                                        Create the first student
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    <!-- Create Student Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Create New Student</h3>
                    <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="createStudent">
                    <div class="p-6 space-y-6">
                        <!-- Personal Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Personal Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">
                                        Registration Number *
                                    </label>
                                    <input type="text" 
                                           wire:model="registration_number"
                                           id="registration_number"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="e.g., BAIT/23U/F0001">
                                    @error('registration_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Full Name *
                                    </label>
                                    <input type="text" 
                                           wire:model="name"
                                           id="name"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Student full name">
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                                        Gender *
                                    </label>
                                    <select wire:model="gender"
                                            id="gender"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email Address *
                                    </label>
                                    <input type="email" 
                                           wire:model="email"
                                           id="email"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="student@slu.ac.ug">
                                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone Number
                                    </label>
                                    <input type="text" 
                                           wire:model="phone"
                                           id="phone"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="+256700000000">
                                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">
                                    Date of Birth
                                </label>
                                <input type="date" 
                                       wire:model="dob"
                                       id="dob"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('dob') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Academic Information</h4>
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
                                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Course/Program *
                                    </label>
                                    <select wire:model="course_id"
                                            id="course_id"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label for="current_year" class="block text-sm font-medium text-gray-700 mb-1">
                                        Current Year *
                                    </label>
                                    <select wire:model="current_year"
                                            id="current_year"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @for($year = 1; $year <= 6; $year++)
                                            <option value="{{ $year }}">Year {{ $year }}</option>
                                        @endfor
                                    </select>
                                    @error('current_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="current_semester" class="block text-sm font-medium text-gray-700 mb-1">
                                        Current Semester *
                                    </label>
                                    <select wire:model="current_semester"
                                            id="current_semester"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="1">Semester 1</option>
                                        <option value="2">Semester 2</option>
                                    </select>
                                    @error('current_semester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Address Information</h4>
                            <div class="space-y-4">
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Address
                                    </label>
                                    <input type="text" 
                                           wire:model="address"
                                           id="address"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Street address">
                                    @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                                            City
                                        </label>
                                        <input type="text" 
                                               wire:model="city"
                                               id="city"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="City">
                                        @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                                            State/Region
                                        </label>
                                        <input type="text" 
                                               wire:model="state"
                                               id="state"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="State or Region">
                                        @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="zip" class="block text-sm font-medium text-gray-700 mb-1">
                                            ZIP Code
                                        </label>
                                        <input type="text" 
                                               wire:model="zip"
                                               id="zip"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="ZIP code">
                                        @error('zip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                                        Country
                                    </label>
                                    <input type="text" 
                                           wire:model="country"
                                           id="country"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Country">
                                    @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
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
                            Create Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit Student Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Student</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="updateStudent">
                    <div class="p-6 space-y-6">
                        <!-- Similar form structure as create, but with edit_ prefix for IDs -->
                        <!-- Personal Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Personal Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="edit_registration_number" class="block text-sm font-medium text-gray-700 mb-1">
                                        Registration Number *
                                    </label>
                                    <input type="text" 
                                           wire:model="registration_number"
                                           id="edit_registration_number"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('registration_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Full Name *
                                    </label>
                                    <input type="text" 
                                           wire:model="name"
                                           id="edit_name"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_gender" class="block text-sm font-medium text-gray-700 mb-1">
                                        Gender *
                                    </label>
                                    <select wire:model="gender"
                                            id="edit_gender"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email Address *
                                    </label>
                                    <input type="email" 
                                           wire:model="email"
                                           id="edit_email"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone Number
                                    </label>
                                    <input type="text" 
                                           wire:model="phone"
                                           id="edit_phone"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="edit_dob" class="block text-sm font-medium text-gray-700 mb-1">
                                    Date of Birth
                                </label>
                                <input type="date" 
                                       wire:model="dob"
                                       id="edit_dob"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('dob') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Academic Information</h4>
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
                                    <label for="edit_course_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Course/Program *
                                    </label>
                                    <select wire:model="course_id"
                                            id="edit_course_id"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label for="edit_current_year" class="block text-sm font-medium text-gray-700 mb-1">
                                        Current Year *
                                    </label>
                                    <select wire:model="current_year"
                                            id="edit_current_year"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @for($year = 1; $year <= 6; $year++)
                                            <option value="{{ $year }}">Year {{ $year }}</option>
                                        @endfor
                                    </select>
                                    @error('current_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_current_semester" class="block text-sm font-medium text-gray-700 mb-1">
                                        Current Semester *
                                    </label>
                                    <select wire:model="current_semester"
                                            id="edit_current_semester"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="1">Semester 1</option>
                                        <option value="2">Semester 2</option>
                                    </select>
                                    @error('current_semester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Address Information</h4>
                            <div class="space-y-4">
                                <div>
                                    <label for="edit_address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Address
                                    </label>
                                    <input type="text" 
                                           wire:model="address"
                                           id="edit_address"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="edit_city" class="block text-sm font-medium text-gray-700 mb-1">
                                            City
                                        </label>
                                        <input type="text" 
                                               wire:model="city"
                                               id="edit_city"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="edit_state" class="block text-sm font-medium text-gray-700 mb-1">
                                            State/Region
                                        </label>
                                        <input type="text" 
                                               wire:model="state"
                                               id="edit_state"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="edit_zip" class="block text-sm font-medium text-gray-700 mb-1">
                                            ZIP Code
                                        </label>
                                        <input type="text" 
                                               wire:model="zip"
                                               id="edit_zip"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('zip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="edit_country" class="block text-sm font-medium text-gray-700 mb-1">
                                        Country
                                    </label>
                                    <input type="text" 
                                           wire:model="country"
                                           id="edit_country"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
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
                            Update Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>