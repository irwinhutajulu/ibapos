@extends('layouts.app', ['title' => 'Stock Adjustments'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Adjustments</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage inventory adjustments and corrections</p>
    </div>
    
    @can('stocks.adjust')
    <a href="{{ route('stock-adjustments.create') }}" class="btn-primary">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        New Adjustment
    </a>
    @endcan
</div>

<!-- Adjustments Table -->
@php
$tableHeaders = ['Date', 'Code', 'Status'];
$tableRows = $adjustments->map(function($a) {
    return [
        'cells' => [
            [
                'type' => 'link',
                'url' => route('stock-adjustments.show', $a),
                'text' => $a->date
            ],
            [
                'type' => 'link',
                'url' => route('stock-adjustments.show', $a),
                'text' => $a->code
            ],
            [
                'type' => 'badge',
                'text' => ucfirst($a->status),
                'style' => $a->status === 'posted' ? 'success' : ($a->status === 'void' ? 'danger' : 'secondary')
            ]
        ],
        'actions' => collect([
            $a->status === 'draft' ? [
                'type' => 'link',
                'url' => route('stock-adjustments.edit', $a),
                'label' => 'Edit',
                'style' => 'primary',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
            ] : null,
            $a->status === 'draft' ? [
                'type' => 'button',
                'label' => 'Post',
                'style' => 'success',
                'onclick' => "document.getElementById('post-form-{$a->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            ] : null,
            $a->status === 'posted' ? [
                'type' => 'button',
                'label' => 'Void',
                'style' => 'danger',
                'onclick' => "document.getElementById('void-form-{$a->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
            ] : null
        ])->filter()->values()->toArray()
    ];
})->toArray();
@endphp

<x-table 
    :headers="$tableHeaders"
    :rows="$tableRows"
    :pagination="$adjustments"
    empty-message="No adjustments found"
    empty-description="Stock adjustments will appear here when you create inventory corrections."
>
    <x-slot name="empty-action">
        @can('stocks.adjust')
        <a href="{{ route('stock-adjustments.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Your First Adjustment
        </a>
        @endcan
    </x-slot>
</x-table>

<!-- Hidden Forms for Actions -->
@foreach($adjustments as $a)
    @if($a->status === 'draft')
    <form id="post-form-{{ $a->id }}" action="{{ route('stock-adjustments.post', $a) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
    
    @if($a->status === 'posted')
    <form id="void-form-{{ $a->id }}" action="{{ route('stock-adjustments.void', $a) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
@endforeach
@endsection
