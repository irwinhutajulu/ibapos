@extends('layouts.app', ['title' => 'Categories'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Categories</h2>
        <p class="text-gray-600 dark:text-gray-400">Organize your products by category</p>
    </div>
    
    @can('categories.create')
    <a href="{{ route('categories.create') }}" class="btn-primary">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add Category
    </a>
    @endcan
</div>

<!-- Categories Table -->
@php
$tableHeaders = ['Category'];
$tableRows = $categories->map(function($c) {
    return [
        'cells' => [
            [
                'type' => 'avatar',
                'name' => $c->name,
                'subtitle' => 'Product Category'
            ]
        ],
        'actions' => collect([
            auth()->user()->can('categories.update') ? [
                'type' => 'link',
                'url' => route('categories.edit', $c),
                'label' => 'Edit',
                'style' => 'primary',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
            ] : null,
            auth()->user()->can('categories.delete') ? [
                'type' => 'button',
                'label' => 'Delete',
                'style' => 'danger',
                'onclick' => "event.preventDefault(); deleteCategory('{$c->id}', '{$c->name}');",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
            ] : null
        ])->filter()->values()->toArray()
    ];
})->toArray();
@endphp

<x-table 
    :headers="$tableHeaders"
    :rows="$tableRows"
    :pagination="$categories"
    empty-message="No categories found"
    empty-description="Start organizing your products by creating your first category."
>
    <x-slot name="empty-action">
        @can('categories.create')
        <a href="{{ route('categories.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Your First Category
        </a>
        @endcan
    </x-slot>
</x-table>

<!-- Hidden Forms for Actions -->
@foreach($categories as $c)
    @can('categories.delete')
    <form id="delete-form-{{ $c->id }}" action="{{ route('categories.destroy', $c) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @endcan
@endforeach

<!-- Confirmation Modal -->
<x-confirmation-modal 
    id="delete-category-modal"
    title="Delete Category"
    message="Are you sure you want to delete this category?"
    confirm-text="Delete"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="warning"
/>

<script>
function deleteCategory(categoryId, categoryName) {
    openConfirmationModal('delete-category-modal', function() {
        document.getElementById(`delete-form-${categoryId}`).submit();
    }, {
        title: 'Delete Category',
        message: `Are you sure you want to delete the category "${categoryName}"? This action cannot be undone.`
    });
}
</script>
@endsection
