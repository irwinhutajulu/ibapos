@extends('layouts.app', ['title' => 'Purchase '.$purchase->invoice_no])

@section('content')
<div class="bg-white border rounded-md p-4">
  @if(session('ok'))
    <script>window.notify(@json(session('ok')), 'success')</script>
  @endif
  <div class="flex items-start justify-between">
    <div>
      <div class="text-sm text-gray-500">Invoice</div>
      <div class="text-lg font-semibold">{{ $purchase->invoice_no }}</div>
      <div class="text-sm text-gray-500 mt-2">Date</div>
      <div>{{ $purchase->date?->format('Y-m-d H:i') }}</div>
    </div>
    <div class="text-right">
      <div class="text-sm text-gray-500">Status</div>
      <div class="font-medium">{{ ucfirst($purchase->status) }}</div>
      <div class="mt-2 text-sm text-gray-500">User</div>
      <div>{{ $purchase->user->name ?? '-' }}</div>
    </div>
  </div>

  <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <div class="text-sm text-gray-500">Supplier</div>
      <div>{{ $purchase->supplier->name ?? '-' }}</div>
    </div>
    <div>
      <div class="text-sm text-gray-500">Freight</div>
      <div>{{ number_format($purchase->freight_cost ?? 0,2) }}</div>
    </div>
    <div>
      <div class="text-sm text-gray-500">Totals</div>
      <div>Total: {{ number_format($purchase->total ?? 0,2) }} | Weight: {{ number_format($purchase->total_weight ?? 0,3) }}</div>
    </div>
  </div>

  <div class="mt-6 overflow-x-auto border rounded-md">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-3 py-2">Product</th>
          <th class="text-right px-3 py-2">Qty</th>
          <th class="text-right px-3 py-2">Price</th>
          <th class="text-right px-3 py-2">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($purchase->items as $it)
        <tr class="border-t">
          <td class="px-3 py-2">{{ $it->product->name ?? ('#'.$it->product_id) }}</td>
          <td class="px-3 py-2 text-right">{{ number_format($it->qty,3) }}</td>
          <td class="px-3 py-2 text-right">{{ number_format($it->price,2) }}</td>
          <td class="px-3 py-2 text-right">{{ number_format($it->subtotal,2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4 flex items-center gap-2">
    @if($purchase->status === 'draft')
      @can('purchases.receive')
      <form action="{{ route('purchases.receive', $purchase) }}" method="post" onsubmit="return confirm('Mark as received?')">
        @csrf
        <button class="px-3 py-2 border rounded-md text-amber-700">Receive</button>
      </form>
      @endcan
      @can('purchases.post')
      <form action="{{ route('purchases.post', $purchase) }}" method="post" onsubmit="return confirm('Post this purchase?')">
        @csrf
        <button class="px-3 py-2 border rounded-md text-green-700">Post</button>
      </form>
      @endcan
      @can('purchases.void')
      <form action="{{ route('purchases.void', $purchase) }}" method="post" onsubmit="return confirm('Void this purchase?')">
        @csrf
        <button class="px-3 py-2 border rounded-md text-red-700">Void</button>
      </form>
      @endcan
    @elseif($purchase->status === 'received')
      @can('purchases.post')
      <form action="{{ route('purchases.post', $purchase) }}" method="post" onsubmit="return confirm('Post this purchase?')">
        @csrf
        <button class="px-3 py-2 border rounded-md text-green-700">Post</button>
      </form>
      @endcan
      @can('purchases.void')
      <form action="{{ route('purchases.void', $purchase) }}" method="post" onsubmit="return confirm('Void this purchase?')">
        @csrf
        <button class="px-3 py-2 border rounded-md text-red-700">Void</button>
      </form>
      @endcan
    @elseif($purchase->status === 'posted')
      @can('purchases.void')
      <form action="{{ route('purchases.void', $purchase) }}" method="post" onsubmit="return confirm('Void this purchase?')">
        @csrf
        <button class="px-3 py-2 border rounded-md text-red-700">Void</button>
      </form>
      @endcan
    @endif
    <a href="{{ route('purchases.index') }}" class="ml-auto underline text-gray-600">Back</a>
  </div>
</div>
@endsection
