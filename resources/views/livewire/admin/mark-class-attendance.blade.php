<!-- resources/views/livewire/admin/mark-class-attendance.blade.php -->
<div class="">
    <div class="">
        <!-- Header with Progress -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Mark Class Attendance</h2>
                <button wire:click="resetComponent" 
                        class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                    Start Over
                </button>
            </div>

            <!-- Progress Steps -->
            <div class="flex items-center space-x-2">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        1
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step >= 1 ? 'text-blue-600' : 'text-gray-500' }}">Course</span>
                </div>
                <div class="flex-1 h-1 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        2
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step >= 2 ? 'text-blue-600' : 'text-gray-500' }}">Course Unit</span>
                </div>
                <div class="flex-1 h-1 {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        3
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step >= 3 ? 'text-blue-600' : 'text-gray-500' }}">Session Details</span>
                </div>
                <div class="flex-1 h-1 {{ $step >= 4 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 4 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        4
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step >= 4 ? 'text-blue-600' : 'text-gray-500' }}">Mark</span>
                </div>
            </div>
        </div>

        <!-- STEP 1: Select Course -->
        @if($step === 1)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 1: Select Course</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($courses as $course)
                        <div wire:click="selectCourse({{ $course->id }})" 
                             class="p-5 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 cursor-pointer transition">
                            <div class="font-bold text-gray-900 text-lg">{{ $course->code }}</div>
                            <div class="text-sm text-gray-700 mt-2">{{ $course->name }}</div>
                            <div class="text-xs text-gray-500 mt-2">
                                <div>{{ $course->department->name }}</div>
                                <div class="mt-1">{{ $course->duration_years }} Years</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($courses->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        No courses available. Please create courses first.
                    </div>
                @endif
            </div>
        @endif

        <!-- STEP 2: Select Course Unit -->
        @if($step === 2)
            <div>
                <!-- Course Info -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-bold text-blue-900">{{ $selectedCourse->code }} - {{ $selectedCourse->name }}</div>
                            <div class="text-sm text-blue-700 mt-1">{{ $selectedCourse->department->name }}</div>
                        </div>
                        <button wire:click="goToStep(1)" 
                                class="px-3 py-1.5 text-sm bg-white text-blue-600 rounded hover:bg-blue-100 transition">
                            Change Course
                        </button>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 2: Select Course Unit</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($courseUnits as $unit)
                        <div wire:click="selectCourseUnit({{ $unit->id }})"
                             class="p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 cursor-pointer transition">
                            <div class="font-bold text-gray-900">{{ $unit->code }}</div>
                            <div class="text-sm text-gray-700 mt-2">{{ $unit->name }}</div>
                            <div class="text-xs text-gray-500 mt-2">
                                <div>{{ $unit->department->name }}</div>
                                <div>Credits: {{ $unit->credits }}</div>
                                <div class="mt-1">
                                    Year {{ $unit->pivot->default_year ?? 'N/A' }}, Semester {{ $unit->pivot->default_semester ?? 'N/A' }}
                                </div>
                                @if($unit->lecturer)
                                    <div class="mt-1 text-gray-600">
                                        Default: {{ $unit->lecturer->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($courseUnits->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        No course units found for this course.
                    </div>
                @endif
            </div>
        @endif

        <!-- STEP 3: Select Lecturer & Session Details -->
        @if($step === 3)
            <div>
                <!-- Course Unit Info -->
                <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-bold text-green-900">{{ $selectedCourseUnit->code }} - {{ $selectedCourseUnit->name }}</div>
                            <div class="text-sm text-green-700 mt-1">
                                {{ $selectedCourseUnit->department->name }} • {{ $selectedCourseUnit->credits }} Credits
                            </div>
                        </div>
                        <button wire:click="goToStep(2)" 
                                class="px-3 py-1.5 text-sm bg-white text-green-600 rounded hover:bg-green-100 transition">
                            Change Unit
                        </button>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 3: Session Details</h3>

                <div class="max-w-3xl mx-auto bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div class="space-y-4">
                        <!-- Lecturer Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Select Lecturer for this Session *
                            </label>
                            <select wire:model="selectedLecturerId"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select Lecturer --</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedLecturerId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <!-- Week -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Week *</label>
                                <input type="number" 
                                       wire:model="sessionWeek"
                                       min="1" max="52"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('sessionWeek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                                <input type="date" 
                                       wire:model="sessionDate"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('sessionDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Venue -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                                <input type="text" 
                                       wire:model="sessionVenue"
                                       placeholder="e.g., Room 101, Lab A"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Start Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                                <input type="time" 
                                       wire:model="sessionStartTime"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('sessionStartTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- End Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                                <input type="time" 
                                       wire:model="sessionEndTime"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('sessionEndTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Topic -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Session Topic (Optional)</label>
                            <input type="text" 
                                   wire:model="sessionTopic"
                                   placeholder="e.g., Introduction to Laravel, Database Normalization"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button wire:click="createSessionAndContinue" 
                                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-lg">
                                Create Session & Continue to Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- STEP 4: Mark Attendance -->
        @if($step === 4)
            <div>
                <!-- Add Students Button -->
                <div class="mb-4">
                    <button type="button" 
                            wire:click="openAddStudentsModal"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Students (Retakes/Special Cases)
                    </button>
                </div>

                <!-- Manual Students Section -->
                @if(count($manualStudents) > 0)
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-semibold text-yellow-800 mb-2">Manually Added Students</h4>
                        <div class="space-y-2">
                            @foreach($manualStudents as $student)
                                <div class="flex justify-between items-center p-2 bg-white rounded border">
                                    <div>
                                        <span class="font-medium">{{ $student->name }}</span>
                                        <span class="text-sm text-gray-600 ml-2">{{ $student->registration_number }}</span>
                                        <span class="text-xs text-yellow-600 ml-2">(Manual)</span>
                                    </div>
                                    <button type="button" 
                                            wire:click="removeManualStudent({{ $student->id }})"
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Session Summary -->
                <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="font-bold text-purple-900">{{ $selectedCourseUnit->code }} - {{ $selectedCourseUnit->name }}</div>
                            <div class="text-sm text-purple-700 mt-1">
                                {{ $selectedCourse->code }} • {{ $selectedCourse->name }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-purple-900">
                                {{ \Carbon\Carbon::parse($sessionDate)->format('l, F d, Y') }}
                            </div>
                            <div class="text-sm text-purple-700 mt-1">
                                {{ $sessionStartTime }} - {{ $sessionEndTime }}
                                @if($sessionVenue) • {{ $sessionVenue }} @endif
                            </div>
                            @if($sessionTopic)
                                <div class="text-sm text-purple-700">Topic: {{ $sessionTopic }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 4: Mark Student Attendance</h3>

                <!-- Search -->
                <div class="mb-4">
                    <input type="text" 
                           wire:model.live.debounce.300ms="searchStudent"
                           placeholder="Search student by name or registration number..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Bulk Actions -->
                <div class="mb-4 flex flex-wrap gap-2">
                    <button wire:click="markAll('present')" 
                            class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-medium">
                        ✓ Mark All Present
                    </button>
                    <button wire:click="markAll('absent')" 
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-medium">
                        ✗ Mark All Absent
                    </button>
                    <button wire:click="markAll('late')" 
                            class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition text-sm font-medium">
                        ⏰ Mark All Late
                    </button>
                    <button wire:click="markAll('excused')" 
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-medium">
                        📝 Mark All Excused
                    </button>
                </div>

                <!-- Students Table -->
                @if(count($students) > 0 || count($manualStudents) > 0)
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reg No.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendance Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $counter = 1; @endphp
                                <!-- Regular Students -->
                                @foreach($students as $student)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $counter++ }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $student->registration_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            Year {{ $student->current_year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            Sem {{ $student->current_semester }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Regular
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex gap-2">
                                                <button wire:click="$set('attendance.{{ $student->user_id }}', 'present')"
                                                        class="px-3 py-1 rounded text-sm font-medium transition
                                                        {{ ($attendance[$student->user_id] ?? 'absent') === 'present' 
                                                            ? 'bg-green-600 text-white' 
                                                            : 'bg-gray-200 text-gray-700 hover:bg-green-100' }}">
                                                    Present
                                                </button>
                                                <button wire:click="$set('attendance.{{ $student->user_id }}', 'absent')"
                                                        class="px-3 py-1 rounded text-sm font-medium transition
                                                        {{ ($attendance[$student->user_id] ?? 'absent') === 'absent' 
                                                            ? 'bg-red-600 text-white' 
                                                            : 'bg-gray-200 text-gray-700 hover:bg-red-100' }}">
                                                    Absent
                                                </button>
                                                <button wire:click="$set('attendance.{{ $student->user_id }}', 'late')"
                                                        class="px-3 py-1 rounded text-sm font-medium transition
                                                        {{ ($attendance[$student->user_id] ?? 'absent') === 'late' 
                                                            ? 'bg-yellow-600 text-white' 
                                                            : 'bg-gray-200 text-gray-700 hover:bg-yellow-100' }}">
                                                    Late
                                                </button>
                                                <button wire:click="$set('attendance.{{ $student->user_id }}', 'excused')"
                                                        class="px-3 py-1 rounded text-sm font-medium transition
                                                        {{ ($attendance[$student->user_id] ?? 'absent') === 'excused' 
                                                            ? 'bg-blue-600 text-white' 
                                                            : 'bg-gray-200 text-gray-700 hover:bg-blue-100' }}">
                                                    Excused
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- Manual Students -->
                                {{-- @foreach($manualStudents as $student)
                                    <tr class="hover:bg-gray-50 bg-yellow-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $counter++ }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $student->registration_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            Year {{ $student->current_year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            Sem {{ $student->current_semester }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Manual
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex gap-2">
                                                <button wire:click="$set('attendance.{{ $student->user_id }}', 'present')"
                                                        class="px-3 py-1 rounded text-sm font-medium transition
                                                        {{ ($attendance[$student->user_id] ?? 'absent') === 'present' 
                                                            ? 'bg-green-600 text-white' 
                                                            : 'bg-gray-200 text-gray-700 hover:bg-green-100' }}">
                                                    Present
                                                </button>
                                                <button wire:click="$set('attendance.{{ $student->user_id }}', 'absent')"
                                                        class="px-3 py-1 rounded text-sm font-medium transition
                                                        {{ ($attendance[$student->user_id] ?? 'absent') === 'absent' 
                                                            ? 'bg-red-600 text-white' 
                                                            : 'bg-gray-200 text-gray-700 hover:bg-red-100' }}">
                                                    Absent
                                                </button>
                                                <button wire:click="$set('attendance.{{ $student->user_id }}', 'late')"
                                                        class="px-3 py-1 rounded text-sm font-medium transition
                                                        {{ ($attendance[$student->user_id] ?? 'absent') === 'late' 
                                                            ? 'bg-yellow-600 text-white' 
                                                            : 'bg-gray-200 text-gray-700 hover:bg-yellow-100' }}">
                                                    Late
                                                </button>
                                                <button wire:click="$set('attendance.{{ $student->user_id }}', 'excused')"
                                                        class="px-3 py-1 rounded text-sm font-medium transition
                                                        {{ ($attendance[$student->user_id] ?? 'absent') === 'excused' 
                                                            ? 'bg-blue-600 text-white' 
                                                            : 'bg-gray-200 text-gray-700 hover:bg-blue-100' }}">
                                                    Excused
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                    @if($showAddStudentsModal)
                        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showAddStudentsModal') }">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                {{-- Backdrop --}}
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeAddStudentsModal"></div>

                                {{-- Modal --}}
                                <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                                    {{-- Header --}}
                                    <div class="px-6 py-4 border-b border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <h3 class="text-lg font-medium text-gray-900">Add Students to Session</h3>
                                            <button wire:click="closeAddStudentsModal" class="text-gray-400 hover:text-gray-500">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Filters & Search --}}
                                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {{-- Search --}}
                                            <div>
                                                <input type="text" wire:model.live="modalSearchStudent" 
                                                    placeholder="Search by name or registration number..."
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>

                                            {{-- Show all years checkbox --}}
                                            <div class="flex items-center">
                                                <input type="checkbox" wire:model.live="modalFilters.show_all_years" 
                                                    id="show_all_years" class="rounded border-gray-300 text-indigo-600">
                                                <label for="show_all_years" class="ml-2 text-sm text-gray-700">Show students from all years</label>
                                            </div>
                                        </div>

                                        @if($modalFilters['show_all_years'])
                                        <div class="grid grid-cols-2 gap-4 mt-4">
                                            <select wire:model.live="modalFilters.year" class="border-gray-300 rounded-md">
                                                <option value="">All Years</option>
                                                <option value="1">Year 1</option>
                                                <option value="2">Year 2</option>
                                                <option value="3">Year 3</option>
                                                <option value="4">Year 4</option>
                                            </select>

                                            <select wire:model.live="modalFilters.semester" class="border-gray-300 rounded-md">
                                                <option value="">All Semesters</option>
                                                <option value="1">Semester 1</option>
                                                <option value="2">Semester 2</option>
                                            </select>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Students List --}}
                                    <div class="px-6 py-4 overflow-y-auto max-h-96">
                                        @if($availableStudents->isNotEmpty())
                                            <div class="mb-4 flex gap-2">
                                                <button wire:click="selectAllAvailableStudents" 
                                                        class="px-3 py-1 text-sm bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200">
                                                    Select All
                                                </button>
                                                <button wire:click="deselectAllAvailableStudents" 
                                                        class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                                    Deselect All
                                                </button>
                                                <span class="text-sm text-gray-500 self-center ml-auto">
                                                    {{ count($selectedStudentsToAdd) }} selected
                                                </span>
                                            </div>

                                            <div class="space-y-2">
                                                @foreach($availableStudents as $student)
                                                <div class="flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer"
                                                    wire:click="toggleStudentSelection({{ $student->user_id }})">
                                                    <input type="checkbox" 
                                                        @if(in_array($student->user_id, $selectedStudentsToAdd)) checked @endif
                                                        class="rounded border-gray-300 text-indigo-600 pointer-events-none">
                                                    <div class="ml-3 flex-1">
                                                        <p class="font-medium text-gray-900">{{ $student->name }}</p>
                                                        <p class="text-sm text-gray-500">
                                                            {{ $student->registration_number }} • 
                                                            Year {{ $student->current_year }} Sem {{ $student->current_semester }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-8 text-gray-500">
                                                <p>No available students found</p>
                                                <p class="text-sm mt-2">All eligible students are already added to this session</p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Footer --}}
                                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                                        <button wire:click="closeAddStudentsModal" 
                                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                            Cancel
                                        </button>
                                        <button wire:click="addSelectedStudents" 
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                                                @if(empty($selectedStudentsToAdd)) disabled @endif>
                                            Add {{ count($selectedStudentsToAdd) }} Student(s)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @php
                        $totalStudents = count($students);
                        $presentCount = collect($attendance)->filter(fn($s) => $s === 'present')->count();
                        $absentCount = collect($attendance)->filter(fn($s) => $s === 'absent')->count();
                        $lateCount = collect($attendance)->filter(fn($s) => $s === 'late')->count();
                        $excusedCount = collect($attendance)->filter(fn($s) => $s === 'excused')->count();
                    @endphp
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</div>
                                <div class="text-xs text-gray-600">Total Students</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-600">{{ $presentCount }}</div>
                                <div class="text-xs text-gray-600">Present</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-red-600">{{ $absentCount }}</div>
                                <div class="text-xs text-gray-600">Absent</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-yellow-600">{{ $lateCount }}</div>
                                <div class="text-xs text-gray-600">Late</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-600">{{ $excusedCount }}</div>
                                <div class="text-xs text-gray-600">Excused</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ $totalStudents > 0 ? round(($presentCount + $lateCount) / $totalStudents * 100, 1) : 0 }}%
                                </div>
                                <div class="text-xs text-gray-600">Attendance Rate</div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 flex justify-end">
                        <button wire:click="saveAttendance" 
                                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-lg">
                            💾 Save Attendance ({{ $totalStudents }} students)
                        </button>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <div class="text-4xl mb-3">👥</div>
                        <div>No students found for this course unit.</div>
                        <div class="text-sm mt-2">Click "Add Students" button above to manually add students for retakes or special cases.</div>
                    </div>
                @endif
            </div>
        @endif
    </div>
    
</div>

