@extends('layouts.app', ['title' => 'Purchases'])

@section('content')
<div x-data="purchasesRealtime()" x-init="init()">
  <div class="flex items-center justify-between mb-4">
    <form method="get" class="flex flex-wrap items-end gap-2">
      <div class="flex flex-col">
        <label class="text-xs text-gray-600">Search</label>
        <input name="q" value="{{ $q ?? '' }}" placeholder="Invoice or supplier" class="px-3 py-2 border rounded-md text-sm"/>
      </div>
      <div class="flex flex-col">
        <label class="text-xs text-gray-600">Status</label>
        <select name="status" class="px-2 py-2 border rounded-md text-sm">
          <option value="">All</option>
          @foreach(['draft','received','posted','void'] as $st)
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
    @can('purchases.create')
      <a href="{{ route('purchases.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm">New Purchase</a>
    @endcan
  </div>

  @if(session('ok'))
    <script>window.notify(@json(session('ok')), 'success')</script>
  @endif

  <div class="overflow-x-auto bg-white border rounded-md">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-3 py-2">Date</th>
          <th class="text-left px-3 py-2">Invoice</th>
          <th class="text-left px-3 py-2">Supplier</th>
          <th class="text-right px-3 py-2">Total</th>
          <th class="text-left px-3 py-2">Status</th>
          <th class="px-3 py-2 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($purchases as $p)
        <tr class="border-t">
          <td class="px-3 py-2">{{ $p->date?->format('Y-m-d') }}</td>
          <td class="px-3 py-2"><a class="underline" href="{{ route('purchases.show', $p) }}">{{ $p->invoice_no }}</a></td>
          <td class="px-3 py-2">{{ $p->supplier->name ?? '-' }}</td>
          <td class="px-3 py-2 text-right">{{ number_format($p->total ?? 0,2) }}</td>
          <td class="px-3 py-2">{{ ucfirst($p->status) }}</td>
          <td class="px-3 py-2 text-right">
            @if($p->status === 'draft')
              @can('purchases.receive')
                <form action="{{ route('purchases.receive', $p) }}" method="post" class="inline" onsubmit="return confirm('Mark as received?')">
                  @csrf
                  <button class="text-amber-700 hover:underline">Receive</button>
                </form>
              @endcan
              @can('purchases.post')
                <form action="{{ route('purchases.post', $p) }}" method="post" class="inline ml-2" onsubmit="return confirm('Post this purchase?')">
                  @csrf
                  <button class="text-green-700 hover:underline">Post</button>
                </form>
              @endcan
              @can('purchases.void')
                <form action="{{ route('purchases.void', $p) }}" method="post" class="inline ml-2" onsubmit="return confirm('Void this purchase?')">
                  @csrf
                  <button class="text-red-700 hover:underline">Void</button>
                </form>
              @endcan
            @elseif($p->status === 'received')
              @can('purchases.post')
                <form action="{{ route('purchases.post', $p) }}" method="post" class="inline" onsubmit="return confirm('Post this purchase?')">
                  @csrf
                  <button class="text-green-700 hover:underline">Post</button>
                </form>
              @endcan
              @can('purchases.void')
                <form action="{{ route('purchases.void', $p) }}" method="post" class="inline ml-2" onsubmit="return confirm('Void this purchase?')">
                  @csrf
                  <button class="text-red-700 hover:underline">Void</button>
                </form>
              @endcan
            @elseif($p->status === 'posted')
              @can('purchases.void')
                <form action="{{ route('purchases.void', $p) }}" method="post" class="inline" onsubmit="return confirm('Void this purchase?')">
                  @csrf
                  <button class="text-red-700 hover:underline">Void</button>
                </form>
              @endcan
            @else
              <span class="text-gray-500">No actions</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-3 py-6 text-center text-gray-500">No purchases found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $purchases->links() }}</div>
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
    }
  }
}
</script>
@endpush
