@props([
    'expense_category' => null,
    'action' => '',
    'method' => 'POST',
    'mode' => 'create' // create, edit, show
])

@php
$isEdit = $mode === 'edit';
$isShow = $mode === 'show';
$isCreate = $mode === 'create';
$readonly = $isShow ? 'readonly' : '';
$disabled = $isShow ? 'disabled' : '';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    <div class="grid grid-cols-1 gap-6">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Category Name <span class="text-red-400">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $expense_category->name ?? '') }}" {{ $readonly }}
                class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                placeholder="Category name" @if(!$isShow) required @endif>
            @error('name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Description
            </label>
            <input type="text" name="description" id="description" value="{{ old('description', $expense_category->description ?? '') }}" {{ $readonly }}
                class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                placeholder="Description">
            @error('description')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        @if($isShow && $expense_category)
        <div>
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Created At</label>
            <p class="text-sm text-gray-400 dark:text-gray-400">{{ $expense_category->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Updated At</label>
            <p class="text-sm text-gray-400 dark:text-gray-400">{{ $expense_category->updated_at->format('d M Y, H:i') }}</p>
        </div>
        @endif
    </div>
    @if(!$isShow)
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        <button type="button" onclick="closeModal('category-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Cancel
        </button>
        <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @if($isCreate)
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Category
            @else
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Category
            @endif
        </button>
    </div>
    @else
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        @can('expense_categories.update')
        <button type="button" onclick="openEditCategoryModal({{ $expense_category->id }})"
                class="px-4 py-2 text-sm font-medium text-blue-400 dark:text-blue-400 bg-blue-900/20 dark:bg-blue-900/20 border border-blue-800 dark:border-blue-800 rounded-lg hover:bg-blue-900/40 dark:hover:bg-blue-900/40 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Category
        </button>
        @endcan
        <button type="button" onclick="closeModal('category-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Close
        </button>
    </div>
    @endif
</form>
