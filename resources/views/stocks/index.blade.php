@extends('layouts.app', ['title' => 'Stock Overview'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Overview</h2>
        <p class="text-gray-600 dark:text-gray-400">Monitor your inventory levels and valuations</p>
    </div>
</div>

<!-- Live Search + Styled Table -->
@php
    $searchUrl = route('stocks.index');
    $initialRows = $stocks->map(function($s) {
        $qty = number_format($s->qty, 0);
        $avg = 'Rp ' . number_format($s->avg_cost, 2, ',', '.');
        $valuation = 'Rp ' . number_format((float)$s->qty * (float)$s->avg_cost, 2, ',', '.');

        return [
            'id' => $s->id,
            'cells' => [
                [
                    'type' => 'avatar',
                    'image' => optional($s->product)->image_path ? url('storage/'.optional($s->product)->image_path) : null,
                    'name' => optional($s->product)->name ?? ('#'.$s->product_id),
                    'subtitle' => optional($s->product)->barcode ?? 'No Barcode'
                ],
                $qty,
                $avg,
                $valuation
            ],
                'actions' => [
                [
                    'type' => 'button',
                    // We will open the ledger inside a remote modal
                    'onclick' => "openRemoteModal('ledgerModal', '" . route('stocks.ledger', $s->product_id) . "', 'Ledger: ' + '" . addslashes(optional($s->product)->name ?? ('#'.$s->product_id)) . "')",
                    // Provide a direct URL as well so mobile cards can navigate to the full ledger page
                    'url' => route('stocks.ledger', $s->product_id),
                    'label' => 'View Ledger',
                    'style' => 'primary',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
                ]
            ]
        ];
    })->values();
@endphp

@component('components.live-search-table', [
    'searchUrl' => $searchUrl,
    'searchParams' => [],
    'debounceMs' => 500,
    'minLength' => 0,
    'placeholder' => 'Search by product name or barcode...',
    'headers' => ['Product','Quantity','Avg Cost','Valuation'],
    'initialRows' => $initialRows,
    'initialPagination' => $stocks->toArray(),
    'emptyMessage' => 'No stock data found',
    'emptyDescription' => 'Stock information will appear here once you have products with inventory.'
])
@endcomponent

{{-- Ledger Modal (remote loader) --}}
<x-modal id="ledgerModal" title="Ledger" size="full" :maxHeight="false">
    {{-- content will be loaded via AJAX into the modal body --}}
</x-modal>
@endsection
