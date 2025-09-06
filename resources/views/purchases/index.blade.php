@extends('layouts.app', ['title' => 'Purchases'])

@section('content')
<div x-data="purchasesRealtime()" x-init="init()">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Purchases</h2>
            <p class="text-gray-600 dark:text-gray-400">Manage your purchase transactions</p>
        </div>
        
        @can('purchases.create')
        <a href="{{ route('purchases.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Purchase
        </a>
        @endcan
    </div>

    <!-- Filters Card -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="form-label">Search</label>
                    <input name="q" 
                           value="{{ $q ?? '' }}" 
                           placeholder="Invoice or supplier" 
                           class="form-input"/>
                </div>
                
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['draft','received','posted','void'] as $st)
                            <option value="{{ $st }}" @selected(($status ?? '')===$st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="form-label">From</label>
                    <input type="date" 
                           name="from" 
                           value="{{ $dateFrom ?? '' }}" 
                           class="form-input"/>
                </div>
                
                <div>
                    <label class="form-label">To</label>
                    <input type="date" 
                           name="to" 
                           value="{{ $dateTo ?? '' }}" 
                           class="form-input"/>
                </div>
                
                <div>
                    <button type="submit" class="btn-primary w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                        Filter
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

    <!-- Purchases Table -->
    @php
    $tableHeaders = ['Date', 'Invoice', 'Supplier', 'Total', 'Status'];
    $tableRows = $purchases->map(function($p) {
        return [
            'cells' => [
                [
                    'type' => 'date',
                    'value' => $p->date,
                    'formatted' => $p->date?->format('Y-m-d')
                ],
                [
                    'type' => 'link',
                    'url' => route('purchases.show', $p),
                    'text' => $p->invoice_no
                ],
                $p->supplier->name ?? '-',
                [
                    'type' => 'currency',
                    'value' => $p->total ?? 0,
                    'formatted' => 'Rp ' . number_format($p->total ?? 0, 0, ',', '.')
                ],
                [
                    'type' => 'badge',
                    'text' => ucfirst($p->status),
                    'style' => $p->status === 'posted' ? 'success' : ($p->status === 'void' ? 'danger' : ($p->status === 'received' ? 'warning' : 'secondary'))
                ]
            ],
            'actions' => collect([
                $p->status === 'draft' && auth()->user()->can('purchases.receive') ? [
                    'type' => 'button',
                    'label' => 'Receive',
                    'style' => 'warning',
                    'onclick' => "event.preventDefault(); if(confirm('Mark as received?')) { document.getElementById('receive-form-{$p->id}').submit(); }",
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                ] : null,
                ($p->status === 'draft' || $p->status === 'received') && auth()->user()->can('purchases.post') ? [
                    'type' => 'button',
                    'label' => 'Post',
                    'style' => 'success',
                    'onclick' => "event.preventDefault(); if(confirm('Post this purchase?')) { document.getElementById('post-form-{$p->id}').submit(); }",
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                ] : null,
                ($p->status === 'draft' || $p->status === 'received' || $p->status === 'posted') && auth()->user()->can('purchases.void') ? [
                    'type' => 'button',
                    'label' => 'Void',
                    'style' => 'danger',
                    'onclick' => "event.preventDefault(); if(confirm('Void this purchase?')) { document.getElementById('void-form-{$p->id}').submit(); }",
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                ] : null
            ])->filter()->values()->toArray()
        ];
    })->toArray();
    @endphp

    <x-table 
        :headers="$tableHeaders"
        :rows="$tableRows"
        :pagination="$purchases"
        empty-message="No purchases found"
        empty-description="Start building your purchase history by creating your first purchase."
    >
        <x-slot name="empty-action">
            @can('purchases.create')
            <a href="{{ route('purchases.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Your First Purchase
            </a>
            @endcan
        </x-slot>
    </x-table>

    <!-- Hidden Forms for Actions -->
    @foreach($purchases as $p)
        @if($p->status === 'draft' && auth()->user()->can('purchases.receive'))
        <form id="receive-form-{{ $p->id }}" action="{{ route('purchases.receive', $p) }}" method="POST" style="display: none;">
            @csrf
        </form>
        @endif
        
        @if(($p->status === 'draft' || $p->status === 'received') && auth()->user()->can('purchases.post'))
        <form id="post-form-{{ $p->id }}" action="{{ route('purchases.post', $p) }}" method="POST" style="display: none;">
            @csrf
        </form>
        @endif
        
        @if(($p->status === 'draft' || $p->status === 'received' || $p->status === 'posted') && auth()->user()->can('purchases.void'))
        <form id="void-form-{{ $p->id }}" action="{{ route('purchases.void', $p) }}" method="POST" style="display: none;">
            @csrf
        </form>
        @endif
    @endforeach
</div>
@endsection

@push('scripts')
<script>
function purchasesRealtime(){
  return {
    init(){
      if(!window.Echo || !window.appActiveLocationId) return;
      const ch = window.Echo.private(`location.${window.appActiveLocationId}`);
      ch.listen('.stock.updated', (e)=>{
        window.notify(`Stock updated product ${e.product_id} qty ${e.qty}`, 'info');
      });
      ch.listen('.purchase.posted', (e)=>{
        window.notify(`Purchase ${e.purchase.invoice_no} posted!`, 'success');
      });
      ch.listen('.purchase.voided', (e)=>{
        window.notify(`Purchase ${e.purchase.invoice_no} voided!`, 'warning');
      });
    }
  }
}
</script>
@endpush
