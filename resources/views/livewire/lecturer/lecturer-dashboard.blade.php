<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900">Lecturer Dashboard</h1>
        
        <div class="mt-4 border-t border-gray-200 pt-4">
            <p class="text-lg text-gray-700">Welcome, <span class="font-semibold">{{ $userName }}</span>.</p>
            <p class="text-md text-gray-600 mt-2">You are currently assigned to <span class="font-semibold">{{ $courseCount }}</span> course(s) for this semester.</p>
        </div>

        <div class="mt-6">
            <a href="{{ route('lecturer.courses') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                View My Courses
            </a>
        </div>
    </div>
</div>