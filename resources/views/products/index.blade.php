@extends('layouts.app', ['title' => 'Products'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Products</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage your product inventory</p>
    </div>
    
    @can('products.create')
    <button onclick="openCreateProductModal()" class="btn-primary w-full sm:w-auto">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add Product
    </button>
    @endcan
</div>

<!-- Live Search Filters -->
<div class="card mb-6" x-data="{ 
    categoryId: '{{ $categoryId ?? '' }}', 
    trashed: {{ $trashed ? 'true' : 'false' }},
    updateFilters() {
        const url = new URL(window.location);
        if (this.categoryId) {
            url.searchParams.set('category_id', this.categoryId);
        } else {
            url.searchParams.delete('category_id');
        }
        if (this.trashed) {
            url.searchParams.set('trashed', '1');
        } else {
            url.searchParams.delete('trashed');
        }
        window.location.href = url.toString();
    }
}">
    <div class="card-body">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="sm:w-48">
                <select x-model="categoryId" 
                        @change="updateFilters()"
                        class="form-select bg-gray-100 dark:bg-gray-900 w-full">
                    <option value="">All Categories</option>
                    @foreach(($categories ?? []) as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center">
                <label class="inline-flex items-center">
                    <input type="checkbox" 
                           x-model="trashed"
                           @change="updateFilters()"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Show deleted</span>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Permission Notice -->
@cannot('products.create')
<div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200 rounded-xl">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        You don't have permission to create products. Ask an admin to grant "products.create".
    </div>
</div>
@endcannot

<!-- Live Search Products Table -->
<!-- Mobile-Responsive Products List with Live Search -->
<div x-data="responsiveProductSearch()" x-init="init()">
    <!-- Search Input -->
    <div class="relative mb-6">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input 
            type="text" 
            x-model="searchQuery"
            @input="debouncedSearch()"
            placeholder="Search by name or barcode..."
            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
        >
        
        <!-- Loading Indicator -->
        <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Results Count -->
    <div x-show="pagination && pagination.total > 0" class="mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span x-text="pagination?.from || 0"></span> to <span x-text="pagination?.to || 0"></span> 
            of <span x-text="pagination?.total || 0"></span> results
            <span x-show="searchQuery" class="font-medium">for "<span x-text="searchQuery"></span>"</span>
        </p>
    </div>

    <!-- Desktop Table View (hidden on mobile) -->
    <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Barcode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="product in products" :key="product.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <!-- Product Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <img :src="product.image_url || '{{ asset('images/default-product.svg') }}'" 
                                             :alt="product.name"
                                             class="w-10 h-10 rounded-lg object-cover bg-gray-100 dark:bg-gray-700"
                                             onerror="this.src='{{ asset('images/default-product.svg') }}'">
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="product.name"></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400" x-text="product.deleted_at ? 'Deleted' : (product.barcode || 'No Barcode')"></div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Barcode -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="product.barcode || '-'"></td>
                            
                            <!-- Category -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="product.category_name || '-'"></td>
                            
                            <!-- Price -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(product.price)"></td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <template x-if="!product.deleted_at">
                                        <div class="flex space-x-2">
                                            @can('products.read')
                                            <button @click="showProduct(product.id)" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </button>
                                            @endcan
                                            
                                            @can('products.update')
                                            <button @click="editProduct(product.id)" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-blue-200 dark:border-blue-800 text-xs font-medium rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            @endcan
                                            
                                            @can('products.delete')
                                            <button @click="deleteProduct(product.id, product.name)" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-red-200 dark:border-red-800 text-xs font-medium rounded-lg text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                            @endcan
                                        </div>
                                    </template>
                                    
                                    <template x-if="product.deleted_at">
                                        <div class="flex space-x-2">
                                            <button @click="restoreProduct(product.id, product.name)" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-green-200 dark:border-green-800 text-xs font-medium rounded-lg text-green-600 dark:text-green-400 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/40 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Restore
                                            </button>
                                            
                                            <button @click="forceDeleteProduct(product.id, product.name)" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-red-200 dark:border-red-800 text-xs font-medium rounded-lg text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Force Delete
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View (hidden on desktop) -->
    <div class="lg:hidden space-y-4">
        <template x-for="product in products" :key="product.id">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-start space-x-4">
                    <!-- Product Image -->
                    <div class="flex-shrink-0">
                        <img :src="product.image_url || '{{ asset('images/default-product.svg') }}'" 
                             :alt="product.name"
                             class="w-16 h-16 rounded-lg object-cover bg-gray-100 dark:bg-gray-700"
                             onerror="this.src='{{ asset('images/default-product.svg') }}'">
                    </div>
                    
                    <!-- Product Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="product.name"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="product.deleted_at ? 'Deleted' : (product.barcode || 'No Barcode')"></p>
                                <p x-show="product.category_name" class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="product.category_name"></p>
                            </div>
                            
                            <span x-show="product.deleted_at" class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 rounded-full">
                                Deleted
                            </span>
                        </div>
                        
                        <!-- Price -->
                        <div class="mt-2">
                            <p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(product.price)"></p>
                            <p x-show="product.barcode" class="text-xs text-gray-500 dark:text-gray-400" x-text="'Barcode: ' + product.barcode"></p>
                        </div>
                        
                        <!-- Mobile Actions -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <template x-if="!product.deleted_at">
                                <div class="flex flex-wrap gap-2">
                                    @can('products.read')
                                    <button @click="showProduct(product.id)" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    @endcan
                                    
                                    @can('products.update')
                                    <button @click="editProduct(product.id)" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    @endcan
                                    
                                    @can('products.delete')
                                    <button @click="deleteProduct(product.id, product.name)" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                    @endcan
                                </div>
                            </template>
                            
                            <template x-if="product.deleted_at">
                                <div class="flex flex-wrap gap-2">
                                    <button @click="restoreProduct(product.id, product.name)" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-green-600 dark:text-green-400 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/40 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Restore
                                    </button>
                                    
                                    <button @click="forceDeleteProduct(product.id, product.name)" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Force Delete
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && products.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
            <span x-show="searchQuery">No products found for "<span x-text="searchQuery"></span>"</span>
            <span x-show="!searchQuery">No products found</span>
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            <span x-show="searchQuery">Try adjusting your search criteria.</span>
            <span x-show="!searchQuery">Add a new product to get started.</span>
        </p>
        @can('products.create')
        <div class="mt-6">
            <button onclick="openCreateProductModal()" class="btn-primary w-full sm:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Your First Product
            </button>
        </div>
        @endcan
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Searching products...</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Please wait while we find your products.</p>
    </div>

    <!-- Pagination -->
    <div x-show="pagination && pagination.last_page > 1" class="mt-6 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 rounded-xl">
        <div class="flex flex-1 justify-between sm:hidden">
            <button x-show="pagination.current_page > 1" 
                    @click="goToPage(pagination.current_page - 1)"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Previous
            </button>
            <span x-show="pagination.current_page === 1" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                Previous
            </span>

            <button x-show="pagination.current_page < pagination.last_page" 
                    @click="goToPage(pagination.current_page + 1)"
                    class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Next
            </button>
            <span x-show="pagination.current_page === pagination.last_page" class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                Next
            </span>
        </div>
        
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <span class="font-medium" x-text="pagination?.from || 0"></span> 
                    to <span class="font-medium" x-text="pagination?.to || 0"></span> 
                    of <span class="font-medium" x-text="pagination?.total || 0"></span> results
                </p>
            </div>
            <div class="flex space-x-1">
                <!-- Pagination buttons will be rendered here -->
                <template x-for="page in paginationPages" :key="page">
                    <button @click="goToPage(page)"
                            :class="page === pagination.current_page ? 
                                'bg-blue-500 text-white' : 
                                'bg-white text-gray-700 hover:bg-gray-50'"
                            class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-300 rounded-md">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms for Actions -->
@foreach($products as $p)
    @if(!$p->deleted_at && auth()->user()->can('products.delete'))
    <form id="delete-form-{{ $p->id }}" action="{{ route('products.destroy', $p) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @endif
    
    @if($p->deleted_at)
    <form id="restore-form-{{ $p->id }}" action="{{ route('products.restore', $p->id) }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <form id="force-delete-form-{{ $p->id }}" action="{{ route('products.force-delete', $p->id) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
@endforeach

<!-- JavaScript for Actions -->
<script>
function deleteProduct(id, name) {
    // Open confirmation modal with delete action
    openConfirmationModal('delete-confirmation-modal', function() {
        const form = document.getElementById(`delete-form-${id}`);
        if (form) {
            form.submit();
        } else {
            // For dynamic products from AJAX, create form on the fly
            const dynamicForm = document.createElement('form');
            dynamicForm.method = 'POST';
            dynamicForm.action = `{{ url('products') }}/${id}`;
            dynamicForm.style.display = 'none';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            dynamicForm.appendChild(csrfToken);
            dynamicForm.appendChild(methodField);
            document.body.appendChild(dynamicForm);
            dynamicForm.submit();
        }
    }, {
        title: 'Delete Product',
        message: `Are you sure you want to delete the product "${name}"? It can be restored later.`
    });
}

function restoreProduct(id, name) {
    // Open confirmation modal with restore action
    openConfirmationModal('restore-confirmation-modal', function() {
        const form = document.getElementById(`restore-form-${id}`);
        if (form) {
            form.submit();
        } else {
            // For dynamic products from AJAX
            const dynamicForm = document.createElement('form');
            dynamicForm.method = 'POST';
            dynamicForm.action = `{{ url('products') }}/${id}/restore`;
            dynamicForm.style.display = 'none';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            dynamicForm.appendChild(csrfToken);
            document.body.appendChild(dynamicForm);
            dynamicForm.submit();
        }
    }, {
        title: 'Restore Product',
        message: `Are you sure you want to restore the product "${name}"?`
    });
}

function forceDeleteProduct(id, name) {
    // Open confirmation modal with force delete action
    openConfirmationModal('force-delete-confirmation-modal', function() {
        const form = document.getElementById(`force-delete-form-${id}`);
        if (form) {
            form.submit();
        } else {
            // For dynamic products from AJAX
            const dynamicForm = document.createElement('form');
            dynamicForm.method = 'POST';
            dynamicForm.action = `{{ url('products') }}/${id}/force-delete`;
            dynamicForm.style.display = 'none';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            dynamicForm.appendChild(csrfToken);
            document.body.appendChild(dynamicForm);
            dynamicForm.submit();
        }
    }, {
        title: 'Permanently Delete Product',
        message: `Are you sure you want to permanently delete the product "${name}"? This action cannot be undone.`
    });
}
</script>

<!-- Product Modal -->
<x-modal id="product-modal" title="Product" size="2xl" :max-height="true">
    <div id="modal-content">
        <!-- Content will be loaded here -->
        <div class="flex items-center justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2 text-gray-600 dark:text-gray-400">Loading...</span>
        </div>
    </div>
</x-modal>

<!-- Confirmation Modal for Delete -->
<x-confirmation-modal 
    id="delete-confirmation-modal"
    title="Delete Product"
    message="Are you sure you want to delete this product? It can be restored later."
    confirm-text="Delete"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="warning"
/>

<!-- Confirmation Modal for Force Delete -->
<x-confirmation-modal 
    id="force-delete-confirmation-modal"
    title="Permanently Delete Product"
    message="Are you sure you want to permanently delete this product? This action cannot be undone."
    confirm-text="Delete Forever"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="danger"
/>

<!-- Confirmation Modal for Restore -->
<x-confirmation-modal 
    id="restore-confirmation-modal"
    title="Restore Product"
    message="Are you sure you want to restore this product?"
    confirm-text="Restore"
    cancel-text="Cancel"
    confirm-class="btn-success"
    icon="success"
/>

<script>
// Open Create Product Modal
function openCreateProductModal() {
    updateModalTitle('Add New Product');
    loadModalContent('{{ route("products.create") }}?modal=1');
    openModal('product-modal');
}

// Open Edit Product Modal
function editProduct(id) {
    updateModalTitle('Edit Product');
    loadModalContent(`{{ url('products') }}/${id}/edit?modal=1`);
    openModal('product-modal');
}

// Open Show Product Modal
function showProduct(id) {
    updateModalTitle('Product Details');
    loadModalContent(`{{ url('products') }}/${id}?modal=1`);
    openModal('product-modal');
}

// Update modal title
function updateModalTitle(title) {
    const modalTitle = document.querySelector('#product-modal h3');
    if (modalTitle) {
        modalTitle.textContent = title;
    }
}

// Load content into modal
async function loadModalContent(url) {
    const content = document.getElementById('modal-content');
    
    // Show loading
    content.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2 text-gray-600 dark:text-gray-400">Loading...</span>
        </div>
    `;
    
    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });
        
        if (response.ok) {
            const html = await response.text();
            content.innerHTML = html;
        } else {
            content.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Error Loading Content</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Please try again later.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading modal content:', error);
        content.innerHTML = `
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Connection Error</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Please check your internet connection.</p>
            </div>
        `;
    }
}

// Handle form submission in modal
document.addEventListener('submit', function(e) {
    if (e.target.closest('#product-modal')) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
        submitBtn.disabled = true;
        
        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json().then(data => {
                    // Close modal
                    closeModal('product-modal');
                    
                    // Show floating success message without immediate reload
                    if (data.message) {
                        showFloatingSuccess(data.message);
                        
                        // Reload page after success message is shown
                        setTimeout(() => {
                            location.reload();
                        }, 1500); // Give time for user to see success message
                    } else {
                        location.reload();
                    }
                });
            } else {
                return response.text().then(text => {
                    // Show validation errors
                    const content = document.getElementById('modal-content');
                    content.innerHTML = text;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            // Restore button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }
});
</script>
@endsection

@push('scripts')
<script>
// Alpine.js Component for Responsive Product Search
function responsiveProductSearch() {
    return {
        searchQuery: '',
        products: @json($products->items()),
        pagination: @json($products->toArray()),
        loading: false,
        searchTimeout: null,
        
        init() {
            // Initialize with current data and search query from server
            this.searchQuery = '{{ $search ?? '' }}';
            console.log('Product search initialized with', this.products.length, 'products');
        },
        
        // Debounced search function
        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            
            // Show loading immediately for better feedback
            this.loading = true;
            
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 500); // 500ms delay for better UX
        },
        
        // Perform the actual search
        async performSearch(page = 1) {
            // Don't set loading again if already loading from debounce
            if (!this.loading) {
                this.loading = true;
            }
            
            try {
                const params = new URLSearchParams({
                    search: this.searchQuery,
                    page: page,
                    @if(request('trashed') === 'only')
                    trashed: 'only'
                    @endif
                });
                
                console.log('Searching with params:', params.toString());
                
                const response = await fetch(`{{ route('api.products.search') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) throw new Error('Search failed');
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (!data.success) {
                    throw new Error(data.message || 'Search failed');
                }
                
                // Update products and pagination
                this.products = data.data;
                this.pagination = data.pagination;
                
            } catch (error) {
                console.error('Search error:', error);
                // Show empty state on error but don't break the interface
                this.products = [];
                this.pagination = null;
            } finally {
                this.loading = false;
            }
        },
        
        // Go to specific page
        async goToPage(page) {
            if (page === this.pagination.current_page) return;
            await this.performSearch(page);
        },
        
        // Generate pagination pages array
        get paginationPages() {
            if (!this.pagination) return [];
            
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            const pages = [];
            
            // Always show first page
            if (current > 3) {
                pages.push(1);
                if (current > 4) pages.push('...');
            }
            
            // Show pages around current
            for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                pages.push(i);
            }
            
            // Always show last page
            if (current < last - 2) {
                if (current < last - 3) pages.push('...');
                pages.push(last);
            }
            
            return pages.filter(p => p !== '...' || pages.indexOf(p) === pages.lastIndexOf(p));
        },
        
        // Action methods
        showProduct(id) {
            showProduct(id);
        },
        
        editProduct(id) {
            editProduct(id);
        },
        
        deleteProduct(id, name) {
            deleteProduct(id, name);
        },
        
        restoreProduct(id, name) {
            restoreProduct(id, name);
        },
        
        forceDeleteProduct(id, name) {
            forceDeleteProduct(id, name);
        }
    }
}
</script>
@endpush
