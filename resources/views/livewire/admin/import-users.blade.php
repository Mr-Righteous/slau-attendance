<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Import Users & Data</h2>
            <p class="text-sm text-gray-600 mt-1">Bulk import students, lecturers, courses, and enrollments from CSV files</p>
        </div>

        <!-- Import Type Selection -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Import Type</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <button wire:click="$set('importType', 'students')" 
                        type="button"
                        class="px-4 py-3 rounded-lg border-2 transition {{ $importType === 'students' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 hover:border-gray-300' }}">
                    <div class="font-semibold">Students</div>
                    <div class="text-xs text-gray-500 mt-1">Import students</div>
                </button>

                <button wire:click="$set('importType', 'lecturers')" 
                        type="button"
                        class="px-4 py-3 rounded-lg border-2 transition {{ $importType === 'lecturers' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 hover:border-gray-300' }}">
                    <div class="font-semibold">Lecturers</div>
                    <div class="text-xs text-gray-500 mt-1">Import lecturers</div>
                </button>

                <button wire:click="$set('importType', 'programs')" 
                        type="button"
                        class="px-4 py-3 rounded-lg border-2 transition {{ $importType === 'programs' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 hover:border-gray-300' }}">
                    <div class="font-semibold">Courses</div>
                    <div class="text-xs text-gray-500 mt-1">Import courses</div>
                </button>

                <button wire:click="$set('importType', 'enrollments')" 
                        type="button"
                        class="px-4 py-3 rounded-lg border-2 transition {{ $importType === 'enrollments' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 hover:border-gray-300' }}">
                    <div class="font-semibold">Enrollments</div>
                    <div class="text-xs text-gray-500 mt-1">Enroll students</div>
                </button>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="font-semibold text-blue-900 mb-2">Instructions for {{ ucfirst($importType) }}</h3>
            
            @if($importType === 'students')
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• CSV must include: <code class="bg-blue-100 px-1 rounded">registration_number, name, email, department_code</code></li>
                    <li>• Default password will be the registration number</li>
                    <li>• Students will be assigned the "student" role automatically</li>
                    <li>• Duplicate emails or registration numbers will be skipped</li>
                </ul>
            @elseif($importType === 'lecturers')
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• CSV must include: <code class="bg-blue-100 px-1 rounded">staff_number, name, email, department_code</code></li>
                    <li>• Default password will be the staff number</li>
                    <li>• Lecturers will be assigned the "lecturer" role automatically</li>
                    <li>• Duplicate emails or staff numbers will be skipped</li>
                </ul>
            @elseif($importType === 'programs')
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• CSV must include: <code class="bg-blue-100 px-1 rounded">program_code, program_name, department_code</code></li>
                    <li>• Department must exist before importing programs</li>
                    <li>• Duplicate program codes will be skipped</li>
                </ul>
            @elseif($importType === 'enrollments')
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• CSV must include: <code class="bg-blue-100 px-1 rounded">registration_number, course_code</code></li>
                    <li>• Make sure students and courses are imported first</li>
                    <li>• Duplicate enrollments will be skipped</li>
                    <li>• One student can be enrolled in multiple courses</li>
                </ul>
            @endif
        </div>

        <!-- Download Template -->
        <div class="mb-6">
            <button wire:click="downloadTemplate" 
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download CSV Template
            </button>
        </div>

        <!-- File Upload -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload CSV File</label>
            <div class="flex items-center justify-center w-full">
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="mb-2 text-sm text-gray-500">
                            <span class="font-semibold">Click to upload</span> or drag and drop
                        </p>
                        <p class="text-xs text-gray-500">CSV files only (Max 10MB)</p>
                    </div>
                    <input wire:model="file" type="file" class="hidden" accept=".csv,.txt" />
                </label>
            </div>
            
            @if($file)
                <div class="mt-2 text-sm text-green-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    File selected: {{ $file->getClientOriginalName() }}
                </div>
            @endif
            
            @error('file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Import Button -->
        <div class="mb-6">
            <button wire:click="import" 
                    wire:loading.attr="disabled"
                    type="button"
                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="import">Import {{ ucfirst($importType) }}</span>
                <span wire:loading wire:target="import">Importing...</span>
            </button>
        </div>

        <!-- Import Results -->
        @if(!empty($importResults))
            <div class="border-t pt-6">
                <h3 class="font-semibold text-gray-900 mb-3">Import Results</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="text-2xl font-bold text-green-700">{{ $importResults['success'] }}</div>
                        <div class="text-sm text-green-600">Successfully Imported</div>
                    </div>
                    
                    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="text-2xl font-bold text-yellow-700">{{ $importResults['skipped'] }}</div>
                        <div class="text-sm text-yellow-600">Skipped</div>
                    </div>
                </div>

                @if(!empty($importResults['errors']))
                    <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                        <h4 class="font-semibold text-red-900 mb-2">Errors & Warnings</h4>
                        <div class="max-h-48 overflow-y-auto">
                            <ul class="text-sm text-red-700 space-y-1">
                                @foreach(array_slice($importResults['errors'], 0, 20) as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                                @if(count($importResults['errors']) > 20)
                                    <li class="font-semibold">• ... and {{ count($importResults['errors']) - 20 }} more</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>