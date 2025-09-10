@include('customers._flash_notify')
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
<div class="card mb-6" x-data="{ 
    trashed: {{ $trashed ? 'true' : 'false' }},
    updateFilters() {
        const url = new URL(window.location);
        if (this.trashed) {
            url.searchParams.set('trashed', '1');
        } else {
            url.searchParams.delete('trashed');
        }
        window.location.href = url.toString();
    }
}">
    <div class="card-body">
        <form method="get" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input name="q" 
                       value="{{ $q ?? '' }}" 
                       placeholder="Search customer name..." 
                       class="form-input w-full"/>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center">
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               x-model="trashed"
                               @change="updateFilters()"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Show deleted</span>
                    </label>
                </div>
                
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
            </div>
            
            <!-- Hidden input to preserve trashed state in search -->
            @if($trashed)
            <input type="hidden" name="trashed" value="1">
            @endif
        </form>
    </div>
</div>

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
                'onclick' => "event.preventDefault(); deleteCustomer('{$c->id}', '{$c->name}');",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
            ] : null,
            $c->trashed() ? [
                'type' => 'button',
                'label' => 'Restore',
                'style' => 'success',
                'onclick' => "restoreCustomer('{$c->id}', '{$c->name}');",
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

<!-- Confirmation Modals -->
<x-confirmation-modal 
    id="delete-customer-modal"
    title="Delete Customer"
    message="Are you sure you want to delete this customer?"
    confirm-text="Delete"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="warning"
/>

<x-confirmation-modal 
    id="restore-customer-modal"
    title="Restore Customer"
    message="Are you sure you want to restore this customer?"
    confirm-text="Restore"
    cancel-text="Cancel"
    confirm-class="btn-success"
    icon="success"
/>

<script>
function deleteCustomer(customerId, customerName) {
    openConfirmationModal('delete-customer-modal', function() {
        document.getElementById(`delete-form-${customerId}`).submit();
    }, {
        title: 'Delete Customer',
        message: `Are you sure you want to delete customer "${customerName}"? This action can be undone later.`
    });
}

function restoreCustomer(customerId, customerName) {
    openConfirmationModal('restore-customer-modal', function() {
        document.getElementById(`restore-form-${customerId}`).submit();
    }, {
        title: 'Restore Customer',
        message: `Are you sure you want to restore customer "${customerName}"?`
    });
}
</script>
@endsection
