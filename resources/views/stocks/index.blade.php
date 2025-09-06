@extends('layouts.app', ['title' => 'Stock Overview'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Overview</h2>
        <p class="text-gray-600 dark:text-gray-400">Monitor your inventory levels and valuations</p>
    </div>
</div>

<!-- Search Card -->
<div class="card mb-6">
    <div class="card-body">
        <form method="get" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input name="q" 
                       value="{{ $q ?? '' }}" 
                       placeholder="Search by product name or barcode..." 
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

<!-- Stocks Table -->
@php
$tableHeaders = ['Product', 'Quantity', 'Avg Cost', 'Valuation'];
$tableRows = $stocks->map(function($s) {
    return [
        'cells' => [
            [
                'type' => 'avatar',
                'name' => $s->product->name ?? ('#'.$s->product_id),
                'subtitle' => $s->product->barcode ?? 'No Barcode'
            ],
            [
                'type' => 'text',
                'text' => number_format($s->qty, 0),
                'align' => 'right'
            ],
            [
                'type' => 'currency',
                'value' => $s->avg_cost,
                'formatted' => 'Rp ' . number_format($s->avg_cost, 2, ',', '.'),
                'align' => 'right'
            ],
            [
                'type' => 'currency',
                'value' => (float)$s->qty * (float)$s->avg_cost,
                'formatted' => 'Rp ' . number_format((float)$s->qty * (float)$s->avg_cost, 2, ',', '.'),
                'align' => 'right'
            ]
        ],
        'actions' => [
            [
                'type' => 'link',
                'url' => route('stocks.ledger', $s->product_id),
                'label' => 'View Ledger',
                'style' => 'primary',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
            ]
        ]
    ];
})->toArray();
@endphp

<x-table 
    :headers="$tableHeaders"
    :rows="$tableRows"
    :pagination="$stocks"
    empty-message="No stock data found"
    empty-description="Stock information will appear here once you have products with inventory."
/>
@endsection
