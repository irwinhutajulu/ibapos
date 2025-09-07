@extends('layouts.app', ['title' => 'Products'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Products</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage your product inventory</p>
    </div>
    
    @can('products.create')
    <button onclick="openCreateProductModal()" class="btn-primary">
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
@php
$searchParams = [
    'category_id' => $categoryId ?? '',
    'trashed' => $trashed ? '1' : ''
];

$tableHeaders = ['Product', 'Barcode', 'Category', 'Price', 'Actions'];

$initialRows = $products->map(function($p) {
    return [
        'id' => $p->id,
        'cells' => [
            [
                'type' => 'avatar',
                'image' => $p->image_url,
                'name' => $p->name,
                'subtitle' => $p->deleted_at ? 'Deleted' : ($p->sku ?? 'No SKU')
            ],
            $p->barcode ?? '-',
            $p->category->name ?? '-',
            [
                'type' => 'currency',
                'value' => $p->price,
                'formatted' => 'Rp ' . number_format($p->price, 0, ',', '.')
            ]
        ],
        'actions' => collect([
            !$p->deleted_at && auth()->user()->can('products.read') ? [
                'type' => 'button',
                'onclick' => 'showProduct(' . $p->id . ')',
                'label' => 'View',
                'style' => 'secondary',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'
            ] : null,
            !$p->deleted_at && auth()->user()->can('products.update') ? [
                'type' => 'button',
                'onclick' => 'editProduct(' . $p->id . ')',
                'label' => 'Edit',
                'style' => 'primary',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
            ] : null,
            !$p->deleted_at && auth()->user()->can('products.delete') ? [
                'type' => 'button',
                'label' => 'Delete',
                'style' => 'danger',
                'onclick' => 'deleteProduct(' . $p->id . ', "' . addslashes($p->name) . '")',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
            ] : null,
            $p->deleted_at ? [
                'type' => 'button',
                'label' => 'Restore',
                'style' => 'success',
                'onclick' => 'restoreProduct(' . $p->id . ', "' . addslashes($p->name) . '")',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>'
            ] : null,
            $p->deleted_at ? [
                'type' => 'button',
                'label' => 'Force Delete',
                'style' => 'danger',
                'onclick' => 'forceDeleteProduct(' . $p->id . ', "' . addslashes($p->name) . '")',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
            ] : null
        ])->filter()->values()->toArray()
    ];
})->toArray();

$initialPagination = [
    'current_page' => $products->currentPage(),
    'last_page' => $products->lastPage(),
    'per_page' => $products->perPage(),
    'total' => $products->total(),
    'from' => $products->firstItem(),
    'to' => $products->lastItem()
];
@endphp

<div x-ref="liveSearchTable">
    <x-live-search-table 
        search-url="{{ Route::has('test.api.products.search') ? route('test.api.products.search') : route('api.products.search') }}"
        :search-params="$searchParams"
        placeholder="Search by name, barcode, or SKU..."
        :headers="$tableHeaders"
        :initial-rows="$initialRows"
        :initial-pagination="$initialPagination"
        empty-message="No products found"
        empty-description="Try adjusting your search criteria or add a new product to get started."
    >
        <x-slot name="empty-action">
            @if(Route::has('test.api.products.search'))
            <a href="#" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Your First Product (Test Mode)
            </a>
            @else
                @can('products.create')
                <button onclick="openCreateProductModal()" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Your First Product
                </button>
                @endcan
            @endif
        </x-slot>
    </x-live-search-table>
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
                    
                    // Show floating success message
                    if (data.message) {
                        showFloatingSuccess(data.message);
                    }
                    
                    // Reload page to show updated data
                    setTimeout(() => {
                        location.reload();
                    }, 500); // Small delay to show success message
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
