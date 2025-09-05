@extends('layouts.app')
@section('content')
<div class="bg-white border rounded-md">
  <div class="p-3 border-b font-semibold">Stock Mutations</div>
  <div class="p-3 overflow-auto">
    <table class="w-full text-sm">
      <thead><tr class="text-left border-b"><th>Date</th><th>Product</th><th>From</th><th>To</th><th>Qty</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      @foreach($mutations as $m)
        <tr class="border-b">
          <td>{{ $m->date }}</td>
          <td>{{ $m->product->name ?? $m->product_id }}</td>
          <td>{{ $m->from_location_id }}</td>
          <td>{{ $m->to_location_id }}</td>
          <td>{{ $m->qty }}</td>
          <td>{{ $m->status }}</td>
          <td class="space-x-1">
            @if($m->status==='pending')
            <form method="POST" action="{{ route('stock-mutations.confirm',$m) }}" class="inline">@csrf<button class="px-2 py-1 border rounded">Confirm</button></form>
            <form method="POST" action="{{ route('stock-mutations.reject',$m) }}" class="inline">@csrf<button class="px-2 py-1 border rounded">Reject</button></form>
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <div class="mt-3">{{ $mutations->links() }}</div>
  </div>
</div>
@endsection
