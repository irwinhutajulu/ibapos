@props([
    'product' => null,
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

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" 
      @if(!$isShow) onsubmit="return validateProductForm()" @endif>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Product Image -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Product Image
            </label>
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <img id="image-preview" 
                         src="{{ $product ? ($product->image_url ?? asset('images/default-product.svg')) : asset('images/default-product.svg') }}" 
                         alt="Product preview" 
                         class="w-20 h-20 rounded-lg object-cover border border-gray-600 dark:border-gray-600">
                </div>
                @if(!$isShow)
                <div class="flex-1">
                    <input type="file" 
                           name="image" 
                           id="image-input"
                           accept="image/*"
                           class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
                           onchange="previewImage(this)">
                    <p class="text-xs text-gray-400 dark:text-gray-400 mt-1">PNG, JPG, GIF up to 2MB</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Name -->
        <div class="md:col-span-2">
            <label for="name" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Product Name <span class="text-red-400">*</span>
            </label>
            <input type="text" 
                   name="name" 
                   id="name"
                   value="{{ old('name', $product->name ?? '') }}" 
                   {{ $readonly }}
                   class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                   placeholder="Enter product name"
                   @if(!$isShow) required @endif>
            @error('name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Barcode -->
        <div>
            <label for="barcode" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Barcode
            </label>
            <input type="text" 
                   name="barcode" 
                   id="barcode"
                   value="{{ old('barcode', $product->barcode ?? '') }}" 
                   {{ $readonly }}
                   class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                   placeholder="Enter barcode">
            @error('barcode')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Unit -->
        <div>
            <label for="unit" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Unit
            </label>
            <input type="text" 
                   name="unit" 
                   id="unit"
                   value="{{ old('unit', $product->unit ?? '') }}" 
                   {{ $readonly }}
                   class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                   placeholder="pcs, kg, liter, etc.">
            @error('unit')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Category -->
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Category <span class="text-red-400">*</span>
            </label>
            <select name="category_id" 
                    id="category_id"
                    {{ $disabled }}
                    class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                    @if(!$isShow) required @endif>
                <option value="">Select Category</option>
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}" {{ old('category_id', $product->category_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Weight -->
        <div>
            <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Weight (kg)
            </label>
            <input type="number" 
                   name="weight" 
                   id="weight"
                   value="{{ old('weight', $product->weight ?? '') }}" 
                   {{ $readonly }}
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white @if($isShow) bg-gray-50 dark:bg-gray-600 @endif"
                   placeholder="0.000"
                   step="0.001"
                   min="0">
            @error('weight')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Price -->
        <div>
            <label for="price" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Price <span class="text-red-400">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-400 dark:text-gray-400">Rp</span>
                <input type="number" 
                       name="price" 
                       id="price"
                       value="{{ old('price', $product->price ?? '') }}" 
                       {{ $readonly }}
                       class="w-full pl-10 pr-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white @if($isShow) bg-gray-600 dark:bg-gray-600 @endif"
                       placeholder="0"
                       min="0"
                       step="1"
                       @if(!$isShow) required @endif>
            </div>
            @error('price')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if($isShow && $product)
        <!-- Additional Info for Show Mode -->
        <div>
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Created At</label>
            <p class="text-sm text-gray-400 dark:text-gray-400">{{ $product->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Updated At</label>
            <p class="text-sm text-gray-400 dark:text-gray-400">{{ $product->updated_at->format('d M Y, H:i') }}</p>
        </div>
        @endif
    </div>

    @if(!$isShow)
    <!-- Form Actions -->
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        <button type="button" 
                onclick="closeModal('product-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Cancel
        </button>
        <button type="submit" 
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @if($isCreate)
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Product
            @else
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Product
            @endif
        </button>
    </div>
    @else
    <!-- Show Mode Actions -->
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        @can('products.update')
        <button type="button" 
                onclick="editProduct({{ $product->id }})"
                class="px-4 py-2 text-sm font-medium text-blue-400 dark:text-blue-400 bg-blue-900/20 dark:bg-blue-900/20 border border-blue-800 dark:border-blue-800 rounded-lg hover:bg-blue-900/40 dark:hover:bg-blue-900/40 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Product
        </button>
        @endcan
        <button type="button" 
                onclick="closeModal('product-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Close
        </button>
    </div>
    @endif
</form>

<!-- Product form helper functions moved to resources/js/app-helpers.js and bundled in app.js -->
