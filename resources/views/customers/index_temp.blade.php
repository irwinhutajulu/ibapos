@extends('layouts.app', ['title' => 'Customers'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Customers</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage your customer database</p>
    </div>
    
    <a href="{{ route('customers.create') }}" class="btn-primary">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add Customer
    </a>
</div>

<!-- Search Card -->
<div class="card mb-6">
    <div class="card-body">
        <form method="get" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input name="q" 
                       value="{{ $q ?? '' }}" 
                       placeholder="Search customer name..." 
                       class="form-input w-full"/>
            </div>
            
            <div>
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

<!-- Customers Table -->
@php
$tableHeaders = ['Customer', 'Phone', 'Address'];
$tableRows = $customers->map(function($c) {
    return [
        'cells' => [
            [
                'type' => 'avatar',
                'name' => $c->name,
                'subtitle' => $c->trashed() ? 'Deleted Customer' : 'Active Customer'
            ],
            $c->phone ?? '-',
            $c->address ?? '-'
        ],
        'actions' => collect([
            !$c->trashed() ? [
                'type' => 'link',
                'url' => route('customers.edit', $c),
                'label' => 'Edit',
                'style' => 'primary',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
            ] : null,
            !$c->trashed() ? [
                'type' => 'button',
                'label' => 'Delete',
                'style' => 'danger',
                'onclick' => "event.preventDefault(); if(confirm('Delete this customer?')) { document.getElementById('delete-form-{$c->id}').submit(); }",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
            ] : null,
            $c->trashed() ? [
                'type' => 'button',
                'label' => 'Restore',
                'style' => 'success',
                'onclick' => "document.getElementById('restore-form-{$c->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>'
            ] : null
        ])->filter()->values()->toArray()
    ];
})->toArray();
@endphp

<x-table 
    :headers="$tableHeaders"
    :rows="$tableRows"
    :pagination="$customers"
    empty-message="No customers found"
    empty-description="Start building your customer database by adding your first customer."
>
    <x-slot name="empty-action">
        <a href="{{ route('customers.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Your First Customer
        </a>
    </x-slot>
</x-table>

<!-- Hidden Forms for Actions -->
@foreach($customers as $c)
    @if(!$c->trashed())
    <form id="delete-form-{{ $c->id }}" action="{{ route('customers.destroy', $c) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    @else
    <form id="restore-form-{{ $c->id }}" action="{{ route('customers.restore', $c->id) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
@endforeach
@endsection
