<div class="">
    <div class="">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 pb-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Manage Departments</h2>
                <p class="text-sm text-gray-600 mt-1">Create and manage academic departments</p>
            </div>
            <button wire:click="openCreateModal" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Add New Department
            </button>
        </div>

        <!-- Departments Table -->
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Code
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Users
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Courses
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($departments as $department)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $department->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $department->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $department->users_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $department->courses_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button wire:click="editDepartment({{ $department->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition">
                                            Edit
                                        </button>
                                        <button wire:click="deleteDepartment({{ $department->id }})"
                                                wire:confirm="Are you sure you want to delete this department?"
                                                class="text-red-600 hover:text-red-900 transition">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No departments found. 
                                    <button wire:click="openCreateModal" class="text-blue-600 hover:text-blue-800 ml-1">
                                        Create the first department
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $departments->links() }}
            </div>
        </div>
    </div>

    <!-- Create Department Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Create New Department</h3>
                    <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="createDepartment">
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Department Name
                            </label>
                            <input type="text" 
                                   wire:model="name"
                                   id="name"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., Computer Science">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                Department Code
                            </label>
                            <input type="text" 
                                   wire:model="code"
                                   id="code"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., CS">
                            @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                            Create Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit Department Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Department</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="updateDepartment">
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Department Name
                            </label>
                            <input type="text" 
                                   wire:model="name"
                                   id="edit_name"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="edit_code" class="block text-sm font-medium text-gray-700 mb-1">
                                Department Code
                            </label>
                            <input type="text" 
                                   wire:model="code"
                                   id="edit_code"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                            Update Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>