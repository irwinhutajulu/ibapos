@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Locations</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage your business locations</p>
        </div>
        <a href="{{ route('locations.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Location
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Address
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Users
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($locations as $location)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $location->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-300">
                                    {{ $location->address ?: 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($location->users as $user)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                            {{ $user->name }}
                                        </span>
                                    @empty
                                        <span class="text-sm text-gray-500 dark:text-gray-400">No users assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $location->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('locations.show', $location) }}" 
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('locations.edit', $location) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button" 
                                            onclick="openDeleteModal({{ $location->id }}, {{ json_encode($location->name) }})"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            title="Delete Location">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No locations found. <a href="{{ route('locations.create') }}" class="text-blue-600 hover:text-blue-800">Create your first location</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($locations->hasPages())
        <div class="mt-6">
            {{ $locations->links() }}
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-white bg-opacity-10 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative mx-auto p-6 border w-96 max-w-sm shadow-2xl rounded-xl bg-white dark:bg-gray-800 transform transition-all duration-300 scale-95 hover:scale-100">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Delete Location</h3>
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                    Are you sure you want to delete<br>
                    "<span id="locationName" class="font-semibold text-gray-900 dark:text-white"></span>"?
                </p>
                <p class="text-red-500 dark:text-red-400 text-xs mt-2 font-medium">
                    This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-3">
                <button id="cancelDelete" 
                        type="button"
                        class="px-6 py-2.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200 min-w-[80px]">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            id="confirmDelete"
                            class="px-6 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 min-w-[80px]">
                        <span class="delete-text">Delete</span>
                        <svg class="delete-loading w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Fallback for console in older browsers
if (typeof console === 'undefined') {
    window.console = { log: function() {}, error: function() {} };
}

// Global variables for modal elements
let deleteModal = null;
let deleteForm = null;
let locationNameSpan = null;
let cancelButton = null;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    deleteModal = document.getElementById('deleteModal');
    deleteForm = document.getElementById('deleteForm');
    locationNameSpan = document.getElementById('locationName');
    cancelButton = document.getElementById('cancelDelete');
    
    // Add event listeners
    if (cancelButton) {
        cancelButton.addEventListener('click', closeDeleteModal);
    }
    
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                closeDeleteModal();
            }
        });
    }
    
    // Add form submission handler for loading state
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            const submitButton = document.getElementById('confirmDelete');
            const deleteText = submitButton.querySelector('.delete-text');
            const loadingIcon = submitButton.querySelector('.delete-loading');
            
            if (submitButton && deleteText && loadingIcon) {
                submitButton.disabled = true;
                deleteText.textContent = 'Deleting...';
                loadingIcon.classList.remove('hidden');
            }
        });
    }
    
    // Add escape key listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && deleteModal && !deleteModal.classList.contains('hidden')) {
            closeDeleteModal();
        }
    });
});

function openDeleteModal(id, name) {
    // Simple approach: Try to find elements each time
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const nameSpan = document.getElementById('locationName');
    
    if (!modal || !form || !nameSpan) {
        alert('Error: Modal elements not found. Please refresh the page and try again.');
        return false;
    }
    
    // Set the location name
    nameSpan.textContent = name;
    
    // Set the form action URL
    form.action = window.location.origin + '/locations/' + id;
    
    // Show the modal
    modal.classList.remove('hidden');
    
    return true;
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Legacy function name for backwards compatibility
function deleteLocation(id, name) {
    return openDeleteModal(id, name);
}
</script>
@endsection
