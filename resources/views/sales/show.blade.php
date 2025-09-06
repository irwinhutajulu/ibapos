@extends('layouts.app', ['title' => 'Sale '.$sale->invoice_no])

@section('content')
<div class="bg-white border rounded-md p-4" x-data="saleShowRealtime()" x-init="init()">
  <div class="flex items-start justify-between">
    <div>
      <div class="text-sm text-gray-500">Invoice</div>
      <div class="text-lg font-semibold">{{ $sale->invoice_no }}</div>
      <div class="text-sm text-gray-500 mt-2">Date</div>
      <div>{{ $sale->date?->format('Y-m-d H:i') }}</div>
    </div>
    <div class="text-right">
      <div class="text-sm text-gray-500">Status</div>
      <div class="font-medium">{{ ucfirst($sale->status) }}</div>
      <div class="mt-2 text-sm text-gray-500">User</div>
      <div>{{ $sale->user->name ?? '-' }}</div>
    </div>
  </div>

  <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <div class="text-sm text-gray-500">Customer</div>
      <div>{{ $sale->customer->name ?? '-' }}</div>
    </div>
    <div>
      <div class="text-sm text-gray-500">Payment</div>
      <div>Paid: {{ number_format($sale->payment,2) }} | Change: {{ number_format($sale->change,2) }}</div>
    </div>
    <div>
      <div class="text-sm text-gray-500">Totals</div>
      <div>Total: {{ number_format($sale->total,2) }} | Discount: {{ number_format($sale->discount,2) }} | Fee: {{ number_format($sale->additional_fee,2) }}</div>
    </div>
  </div>

  <div class="mt-6 overflow-x-auto border rounded-md">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-3 py-2">Product</th>
          <th class="text-right px-3 py-2">Qty</th>
          <th class="text-right px-3 py-2">Price</th>
          <th class="text-right px-3 py-2">Disc</th>
          <th class="text-right px-3 py-2">Subtotal</th>
          <th class="text-left px-3 py-2">Src Loc</th>
        </tr>
      </thead>
      <tbody>
        @foreach($sale->items as $it)
        <tr class="border-t">
          <td class="px-3 py-2">{{ $it->product->name ?? ('#'.$it->product_id) }}</td>
          <td class="px-3 py-2 text-right">{{ number_format($it->qty,3) }}</td>
          <td class="px-3 py-2 text-right">{{ number_format($it->price,2) }}</td>
          <td class="px-3 py-2 text-right">{{ number_format($it->discount,2) }}</td>
          <td class="px-3 py-2 text-right">{{ number_format($it->subtotal,2) }}</td>
          <td class="px-3 py-2">{{ $it->source_location_id ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    <div class="text-sm text-gray-500 mb-1">Payments</div>
    <div class="overflow-x-auto border rounded-md">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left px-3 py-2">Type</th>
            <th class="text-left px-3 py-2">Reference</th>
            <th class="text-left px-3 py-2">Note</th>
            <th class="text-right px-3 py-2">Amount</th>
            <th class="text-left px-3 py-2">Paid At</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sale->payments as $p)
          <tr class="border-t">
            <td class="px-3 py-2">{{ $p->type }}</td>
            <td class="px-3 py-2">{{ $p->reference ?? '-' }}</td>
            <td class="px-3 py-2">{{ $p->note ?? '-' }}</td>
            <td class="px-3 py-2 text-right">{{ number_format($p->amount,2) }}</td>
            <td class="px-3 py-2">{{ $p->paid_at?->format('Y-m-d H:i') }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">No payments</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-4 flex items-center gap-2">
    @if($sale->status === 'draft')
      <form action="{{ route('sales.post', $sale) }}" method="post" onsubmit="return confirm('Post this sale?')">
        @csrf
        <button class="px-3 py-2 border rounded-md text-green-700">Post</button>
      </form>
      <form action="{{ route('sales.void', $sale) }}" method="post" onsubmit="return confirm('Void this sale?')">
        @csrf
        <button class="px-3 py-2 border rounded-md text-red-700">Void</button>
      </form>
    @elseif($sale->status === 'posted')
      <form action="{{ route('sales.void', $sale) }}" method="post" onsubmit="return confirm('Void this sale?')">
        @csrf
        <button class="px-3 py-2 border rounded-md text-red-700">Void</button>
      </form>
    @endif
    <a href="{{ route('sales.index') }}" class="ml-auto underline text-gray-600">Back</a>
  </div>
</div>
@endsection

@push('scripts')
<script>
function saleShowRealtime(){
  return {
    init(){
      if(!window.Echo || !window.appActiveLocationId) return;
      const ch = window.Echo.private(`location.${window.appActiveLocationId}`);
      ch.listen('.sale.posted', (e)=>{ if(e.id==={{ $sale->id }}) window.notify('This sale was posted', 'success'); })
        .listen('.sale.voided', (e)=>{ if(e.id==={{ $sale->id }}) window.notify('This sale was voided', 'warning'); })
        .listen('.stock.updated', ()=>{});
    }
  }
}
</script>
@endpush
