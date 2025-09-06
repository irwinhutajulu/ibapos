@extends('layouts.app', ['title' => 'Sales'])

@section('content')
<div x-data="salesRealtime()" x-init="init()">
<div class="flex items-center justify-between mb-4">
  <form method="get" class="flex flex-wrap items-end gap-2">
    <div class="flex flex-col">
      <label class="text-xs text-gray-600">Search</label>
      <input name="q" value="{{ $q ?? '' }}" placeholder="Invoice or customer" class="px-3 py-2 border rounded-md text-sm"/>
    </div>
    <div class="flex flex-col">
      <label class="text-xs text-gray-600">Status</label>
      <select name="status" class="px-2 py-2 border rounded-md text-sm">
        <option value="">All</option>
        @foreach(['draft','posted','void'] as $st)
          <option value="{{ $st }}" @selected(($status ?? '')===$st)>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex flex-col">
      <label class="text-xs text-gray-600">From</label>
      <input type="date" name="from" value="{{ $dateFrom ?? '' }}" class="px-3 py-2 border rounded-md text-sm"/>
    </div>
    <div class="flex flex-col">
      <label class="text-xs text-gray-600">To</label>
      <input type="date" name="to" value="{{ $dateTo ?? '' }}" class="px-3 py-2 border rounded-md text-sm"/>
    </div>
    <button class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm">Filter</button>
  </form>
  @can('sales.create')
  <a href="{{ route('pos.index') }}" class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm">New Sale (POS)</a>
  @endcan
</div>

@if(session('ok'))
  <div class="mb-3 p-2 bg-green-50 border border-green-200 text-green-800 text-sm rounded">{{ session('ok') }}</div>
@endif

<div class="overflow-x-auto bg-white border rounded-md">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="text-left px-3 py-2">Date</th>
        <th class="text-left px-3 py-2">Invoice</th>
        <th class="text-left px-3 py-2">Customer</th>
        <th class="text-left px-3 py-2">Total</th>
        <th class="text-left px-3 py-2">Status</th>
        <th class="px-3 py-2 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($sales as $s)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $s->date?->format('Y-m-d') }}</td>
        <td class="px-3 py-2"><a class="underline" href="{{ route('sales.show', $s) }}">{{ $s->invoice_no }}</a></td>
        <td class="px-3 py-2">{{ $s->customer->name ?? '-' }}</td>
        <td class="px-3 py-2">{{ number_format($s->total,2) }}</td>
        <td class="px-3 py-2">{{ ucfirst($s->status) }}</td>
        <td class="px-3 py-2 text-right">
          @if($s->status === 'draft')
            <form action="{{ route('sales.post', $s) }}" method="post" class="inline" onsubmit="return confirm('Post this sale?')">
              @csrf
              <button class="text-green-700 hover:underline">Post</button>
            </form>
            <form action="{{ route('sales.void', $s) }}" method="post" class="inline ml-2" onsubmit="return confirm('Void this sale?')">
              @csrf
              <button class="text-red-700 hover:underline">Void</button>
            </form>
          @elseif($s->status === 'posted')
            <form action="{{ route('sales.void', $s) }}" method="post" class="inline" onsubmit="return confirm('Void this sale?')">
              @csrf
              <button class="text-red-700 hover:underline">Void</button>
            </form>
          @else
            <span class="text-gray-500">No actions</span>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="px-3 py-6 text-center text-gray-500">No sales found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $sales->links() }}</div>
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
