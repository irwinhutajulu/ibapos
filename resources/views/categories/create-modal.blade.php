<form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
    @csrf
    
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Category Name <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            value="{{ old('name') }}"
            required
            class="form-input w-full @error('name') border-red-300 dark:border-red-600 @enderror"
            placeholder="Enter category name"
            autofocus
        >
        @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button 
            type="button" 
            onclick="closeModal('category-modal')"
            class="btn-secondary"
        >
            Cancel
        </button>
        <button 
            type="submit" 
            class="btn-primary"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Category
        </button>
    </div>
</form>
