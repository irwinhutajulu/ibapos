
@props([
    'expense' => null,
    'categories' => [],
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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Category -->
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Category <span class="text-red-400">*</span>
            </label>
            <select name="category_id" id="category_id" {{ $disabled }}
                class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                @if(!$isShow) required @endif>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $expense->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Date -->
        <div>
            <label for="date" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Date <span class="text-red-400">*</span>
            </label>
            <input type="date" name="date" id="date" value="{{ old('date', $expense->date ?? '') }}" {{ $readonly }}
                class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                @if(!$isShow) required @endif>
            @error('date')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Description
            </label>
            <input type="text" name="description" id="description" value="{{ old('description', $expense->description ?? '') }}" {{ $readonly }}
                class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                placeholder="Expense description">
            @error('description')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Amount -->
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Amount <span class="text-red-400">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-400 dark:text-gray-400">Rp</span>
                <input type="number" name="amount" id="amount" value="{{ old('amount', $expense->amount ?? '') }}" {{ $readonly }}
                    class="w-full pl-10 pr-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                    placeholder="0"
                    min="0"
                    step="0.01"
                    @if(!$isShow) required @endif>
            </div>
            @error('amount')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- User (Show only) -->
        @if($isShow && $expense)
        <div>
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">User</label>
            <p class="text-sm text-gray-400 dark:text-gray-400">{{ $expense->user->name ?? '-' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Created At</label>
            <p class="text-sm text-gray-400 dark:text-gray-400">{{ $expense->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Updated At</label>
            <p class="text-sm text-gray-400 dark:text-gray-400">{{ $expense->updated_at->format('d M Y, H:i') }}</p>
        </div>
        @endif
    </div>

    @if(!$isShow)
    <!-- Form Actions -->
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        <button type="button" onclick="closeModal('expense-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Cancel
        </button>
        <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @if($isCreate)
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Expense
            @else
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Expense
            @endif
        </button>
    </div>
    @else
    <!-- Show Mode Actions -->
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        @can('expenses.update')
        <button type="button" onclick="openEditExpenseModal({{ $expense->id }})"
                class="px-4 py-2 text-sm font-medium text-blue-400 dark:text-blue-400 bg-blue-900/20 dark:bg-blue-900/20 border border-blue-800 dark:border-blue-800 rounded-lg hover:bg-blue-900/40 dark:hover:bg-blue-900/40 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Expense
        </button>
        @endcan
        <button type="button" onclick="closeModal('expense-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Close
        </button>
    </div>
    @endif
</form>
