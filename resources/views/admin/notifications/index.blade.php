@extends('layouts.app', ['title' => 'Notifications'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Notifications</h2>
        <p class="text-gray-600 dark:text-gray-400">View and manage your system notifications</p>
    </div>
    
    <div class="flex items-center gap-3">
        @if($notifications->where('read_at', null)->count() > 0)
        <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Mark All as Read
            </button>
        </form>
        @endif
    </div>
</div>

@if(session('ok'))
<div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-lg">
    {{ session('ok') }}
</div>
@endif

<!-- Notifications Table -->
@php
$tableHeaders = ['Type', 'Data', 'Created', 'Status'];
$tableRows = $notifications->map(function($n) {
    $decodedData = json_decode($n->data, true);
    return [
        'cells' => [
            [
                'type' => 'badge',
                'text' => ucfirst(str_replace('_', ' ', $n->type)),
                'style' => match($n->type) {
                    'sale_created' => 'success',
                    'purchase_created' => 'primary',
                    'low_stock' => 'warning',
                    'stock_out' => 'danger',
                    default => 'secondary'
                }
            ],
            [
                'type' => 'raw',
                'content' => '<pre class="whitespace-pre-wrap text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded max-w-md overflow-auto">' . 
                           htmlspecialchars(json_encode($decodedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . 
                           '</pre>'
            ],
            [
                'type' => 'datetime',
                'date' => $n->created_at
            ],
            [
                'type' => 'badge',
                'text' => $n->read_at ? 'Read' : 'Unread',
                'style' => $n->read_at ? 'secondary' : 'warning'
            ]
        ],
        'actions' => collect([
            !$n->read_at ? [
                'type' => 'button',
                'label' => 'Mark as Read',
                'style' => 'primary',
                'onclick' => "document.getElementById('read-form-{$n->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            ] : null
        ])->filter()->values()->toArray(),
        'classes' => $n->read_at ? 'opacity-75' : ''
    ];
})->toArray();
@endphp

<x-table 
    :headers="$tableHeaders"
    :rows="$tableRows"
    :pagination="$notifications"
    empty-message="No notifications found"
    empty-description="Your system notifications will appear here when they are created."
>
</x-table>

<!-- Hidden Forms for Actions -->
@foreach($notifications as $n)
    @if(!$n->read_at)
    <form id="read-form-{{ $n->id }}" action="{{ route('admin.notifications.read', $n->id) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
@endforeach
@endsection
