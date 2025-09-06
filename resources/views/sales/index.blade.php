@extends('layouts.app', ['title' => 'Sales'])

@section('content')
<div x-data="salesRealtime()" x-init="init()">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sales</h2>
            <p class="text-gray-600 dark:text-gray-400">Manage your sales transactions</p>
        </div>
        
        @can('sales.create')
        <a href="{{ route('pos.index') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Sale (POS)
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
                           placeholder="Invoice or customer" 
                           class="form-input"/>
                </div>
                
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['draft','posted','void'] as $st)
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

    <!-- Sales Table -->
    @php
    $tableHeaders = ['Date', 'Invoice', 'Customer', 'Total', 'Status'];
    $tableRows = $sales->map(function($s) {
        return [
            'cells' => [
                [
                    'type' => 'date',
                    'value' => $s->date,
                    'formatted' => $s->date?->format('Y-m-d')
                ],
                [
                    'type' => 'link',
                    'url' => route('sales.show', $s),
                    'text' => $s->invoice_no
                ],
                $s->customer->name ?? '-',
                [
                    'type' => 'currency',
                    'value' => $s->total,
                    'formatted' => 'Rp ' . number_format($s->total, 0, ',', '.')
                ],
                [
                    'type' => 'badge',
                    'text' => ucfirst($s->status),
                    'style' => $s->status === 'posted' ? 'success' : ($s->status === 'void' ? 'danger' : 'secondary')
                ]
            ],
            'actions' => collect([
                $s->status === 'draft' ? [
                    'type' => 'button',
                    'label' => 'Post',
                    'style' => 'success',
                    'onclick' => "event.preventDefault(); if(confirm('Post this sale?')) { document.getElementById('post-form-{$s->id}').submit(); }",
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                ] : null,
                $s->status === 'draft' || $s->status === 'posted' ? [
                    'type' => 'button',
                    'label' => 'Void',
                    'style' => 'danger',
                    'onclick' => "event.preventDefault(); if(confirm('Void this sale?')) { document.getElementById('void-form-{$s->id}').submit(); }",
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                ] : null
            ])->filter()->values()->toArray()
        ];
    })->toArray();
    @endphp

    <x-table 
        :headers="$tableHeaders"
        :rows="$tableRows"
        :pagination="$sales"
        empty-message="No sales found"
        empty-description="Start selling by creating your first sale transaction."
    >
        <x-slot name="empty-action">
            @can('sales.create')
            <a href="{{ route('pos.index') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Your First Sale
            </a>
            @endcan
        </x-slot>
    </x-table>

    <!-- Hidden Forms for Actions -->
    @foreach($sales as $s)
        @if($s->status === 'draft')
        <form id="post-form-{{ $s->id }}" action="{{ route('sales.post', $s) }}" method="POST" style="display: none;">
            @csrf
        </form>
        @endif
        
        @if($s->status === 'draft' || $s->status === 'posted')
        <form id="void-form-{{ $s->id }}" action="{{ route('sales.void', $s) }}" method="POST" style="display: none;">
            @csrf
        </form>
        @endif
    @endforeach
</div>
@endsection

@push('scripts')
<script>
function salesRealtime(){
  return {
    init(){
      if(!window.Echo || !window.appActiveLocationId) return;
      const ch = window.Echo.private(`location.${window.appActiveLocationId}`);
      ch.listen('.sale.posted', (e)=>{
        window.notify(`Sale posted #${e.id} total ${e.total}`, 'success');
      }).listen('.sale.voided', (e)=>{
        window.notify(`Sale voided #${e.id}`, 'warning');
      }).listen('.stock.updated', (e)=>{
        window.notify(`Stock updated product ${e.product_id} qty ${e.qty}`, 'info');
      });
    }
  }
}
</script>
@endpush
