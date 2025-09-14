@extends('layouts.app')

@section('content')
@include('locations._flash_notify')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <a href="{{ route('locations.index') }}" 
               class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Location</h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400">Add a new business location to your system</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('locations.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Location Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Location Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                       placeholder="e.g., Main Store, Warehouse A, Branch Office"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Address
                </label>
                <textarea id="address" 
                          name="address" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                          placeholder="Enter the complete address of this location">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Phone Number
                </label>
                <input type="text" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                       placeholder="e.g., 021-7654321, 0812-3456-7890">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Users Assignment -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Assign Users
                </label>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                    Select users who will have access to this location
                </p>
                
                <div class="border border-gray-300 dark:border-gray-600 rounded-md p-4 max-h-64 overflow-y-auto">
                    @forelse($users as $user)
                        <div class="flex items-center mb-2">
                            <input type="checkbox" 
                                   id="user_{{ $user->id }}" 
                                   name="user_ids[]" 
                                   value="{{ $user->id }}"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}>
                            <label for="user_{{ $user->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                <span class="font-medium">{{ $user->name }}</span>
                                <span class="text-gray-500">{{ $user->email }}</span>
                            </label>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No users available</p>
                    @endforelse
                </div>
                @error('user_ids')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('user_ids.*')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                <a href="{{ route('locations.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Location
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Select All / Deselect All functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add select all button if there are many users
    const userCheckboxes = document.querySelectorAll('input[name="user_ids[]"]');
    if (userCheckboxes.length > 3) {
        const container = document.querySelector('.border.border-gray-300.dark\\:border-gray-600.rounded-md.p-4');
        if (container) {
            const selectAllDiv = document.createElement('div');
            selectAllDiv.className = 'mb-3 pb-3 border-b border-gray-200 dark:border-gray-600';
            selectAllDiv.innerHTML = `
                <button type="button" id="selectAll" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    Select All
                </button>
                <span class="mx-2 text-gray-300">|</span>
                <button type="button" id="deselectAll" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    Deselect All
                </button>
            `;
            container.insertBefore(selectAllDiv, container.firstChild);
            
            document.getElementById('selectAll').addEventListener('click', function() {
                userCheckboxes.forEach(cb => cb.checked = true);
            });
            
            document.getElementById('deselectAll').addEventListener('click', function() {
                userCheckboxes.forEach(cb => cb.checked = false);
            });
        }
    }
});
</script>
@endsection
