@extends('layouts.app', ['title' => 'Reservations'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reservations</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage stock reservations and allocations</p>
    </div>
    
    @can('stocks.adjust')
    <form method="post" action="{{ route('reservations.cleanup') }}">
        @csrf
        <button type="submit" class="btn-warning">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Cleanup Expired
        </button>
    </form>
    @endcan
</div>

<!-- Filters Card -->
<div class="card mb-6">
    <div class="card-body">
        <form method="get" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="form-label">Status Filter</label>
                <select name="status" class="form-select w-full" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['active','consumed','released','expired'] as $st)
                        <option value="{{ $st }}" @selected(($status ?? '')===$st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
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

<!-- Reservations Table -->
@php
$tableHeaders = ['Product', 'Location', 'Quantity', 'Status', 'Expires'];
$tableRows = $reservations->map(function($r) {
    return [
        'cells' => [
            [
                'type' => 'avatar',
                'name' => $r->product_name,
                'subtitle' => 'Reserved Stock'
            ],
            $r->location_name,
            [
                'type' => 'text',
                'text' => number_format($r->qty_reserved, 3),
                'align' => 'right'
            ],
            [
                'type' => 'badge',
                'text' => ucfirst($r->status),
                'style' => $r->status === 'active' ? 'success' : ($r->status === 'consumed' ? 'primary' : ($r->status === 'expired' ? 'danger' : 'secondary'))
            ],
            [
                'type' => 'date',
                'value' => $r->expires_at,
                'formatted' => $r->expires_at ? \Carbon\Carbon::parse($r->expires_at)->format('Y-m-d H:i') : '-'
            ]
        ],
        'actions' => $r->status === 'active' && auth()->user()->can('stocks.adjust') ? collect([
            [
                'type' => 'button',
                'label' => 'Release',
                'style' => 'warning',
                'onclick' => "document.getElementById('release-form-{$r->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>'
            ],
            [
                'type' => 'button',
                'label' => 'Consume',
                'style' => 'primary',
                'onclick' => "document.getElementById('consume-form-{$r->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            ]
        ])->toArray() : []
    ];
})->toArray();
@endphp

<x-table 
    :headers="$tableHeaders"
    :rows="$tableRows"
    :pagination="$reservations"
    empty-message="No reservations found"
    empty-description="Stock reservations will appear here when inventory is allocated for specific purposes."
/>

<!-- Hidden Forms for Actions -->
@foreach($reservations as $r)
    @if($r->status === 'active' && auth()->user()->can('stocks.adjust'))
    <form id="release-form-{{ $r->id }}" action="{{ route('reservations.release', $r->id) }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <form id="consume-form-{{ $r->id }}" action="{{ route('reservations.consume', $r->id) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
@endforeach
@endsection
