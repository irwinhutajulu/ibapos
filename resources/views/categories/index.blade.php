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
                'onclick' => "event.preventDefault(); if(confirm('Delete this category?')) { document.getElementById('delete-form-{$c->id}').submit(); }",
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
@endsection
