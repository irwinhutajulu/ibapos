<form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')
    
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Category Name <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            value="{{ old('name', $category->name) }}"
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Update Category
        </button>
    </div>
</form>
