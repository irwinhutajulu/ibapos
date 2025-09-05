@extends('layouts.app')
@section('content')
<div class="bg-white border rounded-md">
  <div class="p-3 border-b font-semibold">Purchases</div>
  <div class="p-3 overflow-auto">
    <table class="w-full text-sm">
      <thead><tr class="text-left border-b"><th>Invoice</th><th>Date</th><th>Supplier</th><th>Status</th><th>Total</th><th>Aksi</th></tr></thead>
      <tbody>
      @foreach($purchases as $p)
        <tr class="border-b">
          <td>{{ $p->invoice_no }}</td>
          <td>{{ $p->date }}</td>
          <td>{{ $p->supplier->name ?? '-' }}</td>
          <td>{{ $p->status }}</td>
          <td>{{ number_format($p->total,2) }}</td>
          <td class="space-x-1">
            <form method="POST" action="{{ route('purchases.receive',$p) }}" class="inline">@csrf<button class="px-2 py-1 border rounded">Receive</button></form>
            <form method="POST" action="{{ route('purchases.post',$p) }}" class="inline">@csrf<button class="px-2 py-1 border rounded">Post</button></form>
            <form method="POST" action="{{ route('purchases.void',$p) }}" class="inline">@csrf<button class="px-2 py-1 border rounded">Void</button></form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <div class="mt-3">{{ $purchases->links() }}</div>
  </div>
</div>
@endsection
