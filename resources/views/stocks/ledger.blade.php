@extends('layouts.app', ['title' => 'Stock Ledger - ' . $product->name])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Ledger</h2>
        <p class="text-gray-600 dark:text-gray-400">
            Transaction history for {{ $product->name }}
            @if($locationId) 
                <span class="text-sm opacity-75">(Location #{{ $locationId }})</span> 
            @endif
        </p>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="{{ route('stocks.index') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Stocks
        </a>
    </div>
</div>

<!-- Product Info Card -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-xl font-bold">
            {{ strtoupper(substr($product->name, 0, 2)) }}
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $product->name }}</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ $product->code }}</p>
            @if($locationId)
                <span class="inline-block mt-2 px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">
                    Location #{{ $locationId }}
                </span>
            @endif
        </div>
    </div>
</div>

<!-- Stock Ledger Table -->
@php
$tableHeaders = ['Date', 'Reference', 'Qty Change', 'Balance', 'Cost/Unit', 'Total Cost', 'Note'];
$tableRows = $entries->map(function($e) {
    return [
        'cells' => [
            [
                'type' => 'datetime',
                'date' => $e->created_at
            ],
            [
                'type' => 'text',
                'text' => $e->ref_type . ' #' . $e->ref_id,
                'classes' => 'font-mono text-sm'
            ],
            [
                'type' => 'text',
                'text' => ($e->qty_change > 0 ? '+' : '') . number_format($e->qty_change),
                'classes' => 'text-right font-semibold ' . ($e->qty_change > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400')
            ],
            [
                'type' => 'text',
                'text' => number_format($e->balance_after),
                'classes' => 'text-right font-semibold text-gray-900 dark:text-white'
            ],
            [
                'type' => 'currency',
                'amount' => $e->cost_per_unit_at_time,
                'currency' => 'Rp',
                'classes' => 'text-right'
            ],
            [
                'type' => 'currency',
                'amount' => $e->total_cost_effect,
                'currency' => 'Rp',
                'classes' => 'text-right'
            ],
            [
                'type' => 'text',
                'text' => $e->note ?: '-',
                'classes' => 'text-sm text-gray-600 dark:text-gray-400'
            ]
        ]
    ];
})->toArray();
@endphp

<x-table 
    :headers="$tableHeaders"
    :rows="$tableRows"
    :pagination="$entries"
    empty-message="No stock transactions found"
    empty-description="Stock movement history for this product will appear here."
>
</x-table>
@endsection
