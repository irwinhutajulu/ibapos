@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <a href="{{ route('locations.index') }}" 
               class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $location->name }}</h1>
        </div>
        <div class="flex items-center justify-between">
            <p class="text-gray-600 dark:text-gray-400">Location details and user assignments</p>
            <div class="flex space-x-2">
                <a href="{{ route('locations.edit', $location) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Location
                </a>
                <button type="button" 
                        onclick="deleteLocation({{ $location->id }}, '{{ $location->name }}')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Location Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Location Name</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $location->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $location->address ?: 'No address provided' }}
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $location->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $location->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Assigned Users</h2>
                <span class="bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $location->users->count() }} user(s)
                </span>
            </div>
            
            @if($location->users->count() > 0)
                <div class="space-y-3">
                    @foreach($location->users as $user)
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No users assigned to this location</p>
                    <a href="{{ route('locations.edit', $location) }}" 
                       class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        Assign users now
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics or Related Data -->
    <div class="mt-6 bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- These could be dynamic stats -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Products</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">-</p>
                <p class="text-xs text-gray-400">Coming soon</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Current Stock Value</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">-</p>
                <p class="text-xs text-gray-400">Coming soon</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Sales</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">-</p>
                <p class="text-xs text-gray-400">Coming soon</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Assigned Users</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $location->users->count() }}</p>
                <p class="text-xs text-green-600">Active</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-2">Delete Location</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete "<span id="locationName"></span>"? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3 flex gap-2 justify-center">
                <button id="cancelDelete" 
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteLocation(id, name) {
    document.getElementById('locationName').textContent = name;
    document.getElementById('deleteForm').action = '/locations/' + id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('deleteModal').classList.add('hidden');
});

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endsection
