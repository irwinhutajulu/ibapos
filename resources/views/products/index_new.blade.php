@extends('layouts.app', ['title' => 'Products'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Products</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage your product inventory</p>
    </div>
    
    @can('products.create')
    <a href="{{ route('products.create') }}" class="btn-primary">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add Product
    </a>
    @endcan
</div>

<!-- Filters Card -->
<div class="card mb-6">
    <div class="card-body">
        <form method="get" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input name="q" 
                       value="{{ $q ?? '' }}" 
                       placeholder="Search by name or barcode..." 
                       class="form-input w-full"/>
            </div>
            
            <div class="sm:w-48">
                <select name="category_id" class="form-select w-full">
                    <option value="">All Categories</option>
                    @foreach(($categories ?? []) as $id => $name)
                        <option value="{{ $id }}" @selected(($categoryId ?? null) == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" 
                           name="trashed" 
                           value="1"
                           onchange="const u=new URL(window.location);u.searchParams.set('trashed', this.checked?1:''); window.location=u.toString();"
                           @checked($trashed ?? false)
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Show deleted</span>
                </label>
                
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Message -->
@if(session('ok'))
<div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-xl">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('ok') }}
    </div>
</div>
@endif

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

<!-- Products Table -->
<x-table 
    :headers="[
        'Product',
        'Barcode', 
        'Category',
        'Price',
    ]"
    :rows="$products->map(function($p) {
        return [
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
                    'type' => 'link',
                    'url' => route('products.show', $p),
                    'label' => 'View',
                    'style' => 'secondary',
                    'icon' => '<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 12a3 3 0 11-6 0 3 3 0 016 0z\"></path><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z\"></path></svg>'
                ] : null,
                !$p->deleted_at && auth()->user()->can('products.update') ? [
                    'type' => 'link',
                    'url' => route('products.edit', $p),
                    'label' => 'Edit',
                    'style' => 'primary',
                    'icon' => '<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\"></path></svg>'
                ] : null,
                !$p->deleted_at && auth()->user()->can('products.delete') ? [
                    'type' => 'button',
                    'label' => 'Delete',
                    'style' => 'danger',
                    'onclick' => \"event.preventDefault(); if(confirm('Delete this product?')) { document.getElementById('delete-form-{$p->id}').submit(); }\",
                    'icon' => '<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\"></path></svg>'
                ] : null,
                $p->deleted_at ? [
                    'type' => 'button',
                    'label' => 'Restore',
                    'style' => 'success',
                    'onclick' => \"document.getElementById('restore-form-{$p->id}').submit();\",
                    'icon' => '<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15\"></path></svg>'
                ] : null,
                $p->deleted_at ? [
                    'type' => 'button',
                    'label' => 'Force Delete',
                    'style' => 'danger',
                    'onclick' => \"event.preventDefault(); if(confirm('Permanently delete this product? This cannot be undone.')) { document.getElementById('force-delete-form-{$p->id}').submit(); }\",
                    'icon' => '<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\"></path></svg>'
                ] : null
            ])->filter()->values()->toArray()
        ];
    })->toArray()"
    :pagination="$products"
    empty-message="No products found"
    empty-description="Try adjusting your search criteria or add a new product to get started."
>
    <x-slot name="empty-action">
        @can('products.create')
        <a href="{{ route('products.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Your First Product
        </a>
        @endcan
    </x-slot>
</x-table>

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
@endsection
