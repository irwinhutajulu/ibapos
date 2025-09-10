@include('mutations._flash_notify')
@extends('layouts.app', ['title' => 'Stock Mutations'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Mutations</h2>
        <p class="text-gray-600 dark:text-gray-400">Track stock movements between locations</p>
    </div>
</div>

<!-- Mutations Table -->
@php
$tableHeaders = ['Date', 'Product', 'From', 'To', 'Quantity', 'Status'];
$tableRows = $mutations->map(function($m) {
    return [
        'cells' => [
            $m->date,
            $m->product->name ?? ('#' . $m->product_id),
            'Location #' . $m->from_location_id,
            'Location #' . $m->to_location_id,
            [
                'type' => 'text',
                'text' => number_format($m->qty, 0),
                'align' => 'right'
            ],
            [
                'type' => 'badge',
                'text' => ucfirst($m->status),
                'style' => $m->status === 'confirmed' ? 'success' : ($m->status === 'rejected' ? 'danger' : 'warning')
            ]
        ],
        'actions' => $m->status === 'pending' ? collect([
            [
                'type' => 'button',
                'label' => 'Confirm',
                'style' => 'success',
                'onclick' => "document.getElementById('confirm-form-{$m->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            ],
            [
                'type' => 'button',
                'label' => 'Reject',
                'style' => 'danger',
                'onclick' => "document.getElementById('reject-form-{$m->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
            ]
        ])->toArray() : []
    ];
})->toArray();
@endphp

<x-table 
    :headers="$tableHeaders"
    :rows="$tableRows"
    :pagination="$mutations"
    empty-message="No mutations found"
    empty-description="Stock mutations will appear here when products are moved between locations."
/>

<!-- Hidden Forms for Actions -->
@foreach($mutations as $m)
    @if($m->status === 'pending')
    <form id="confirm-form-{{ $m->id }}" action="{{ route('stock-mutations.confirm', $m) }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <form id="reject-form-{{ $m->id }}" action="{{ route('stock-mutations.reject', $m) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
@endforeach
@endsection
