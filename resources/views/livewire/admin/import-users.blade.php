<div>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6">Import Students</h2>
        
        <div class="mb-6">
            <p class="text-gray-600 mb-4">
                Import student data from CSV file. Only student import is supported as other data is pre-seeded.
            </p>
            
            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                <h3 class="font-semibold mb-2">Required CSV Format:</h3>
                <p class="text-sm">registration_number, name, email, department_code, course_code, current_year, current_semester, academic_year, gender, phone, nationality</p>
            </div>
        </div>

        <form wire:submit="import">
            <div class="grid grid-cols-1 gap-6">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        CSV File
                    </label>
                    <input type="file" wire:model="file" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Import Button -->
                <div>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Import Students</span>
                        <span wire:loading>Importing...</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Download Template -->
        <div class="mt-4">
            <button wire:click="downloadTemplate" 
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Download CSV Template
            </button>
        </div>

        <!-- Results -->
        @if (!empty($importResults))
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold mb-2">Import Results:</h3>
                <p class="text-green-600">Success: {{ $importResults['success'] ?? 0 }}</p>
                <p class="text-yellow-600">Skipped: {{ $importResults['skipped'] ?? 0 }}</p>
                
                @if (!empty($importResults['errors']))
                    <div class="mt-2">
                        <h4 class="font-medium text-red-600">Errors:</h4>
                        <ul class="text-sm text-red-600 max-h-32 overflow-y-auto">
                            @foreach ($importResults['errors'] as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>