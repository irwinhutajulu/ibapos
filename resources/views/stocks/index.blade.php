@extends('layouts.app')
@section('content')
<div class="bg-white border rounded-md">
  <div class="p-3 border-b flex items-center justify-between">
    <div class="font-semibold">Stock Overview</div>
    <form method="GET" class="text-sm">
      <input type="text" name="q" value="{{ $q }}" placeholder="Search name/barcode" class="border rounded p-1" />
      <button class="px-2 py-1 border rounded">Search</button>
    </form>
  </div>
  <div class="p-3 overflow-auto text-sm">
    <table class="w-full">
      <thead><tr class="text-left border-b"><th>Product</th><th class="text-right">Qty</th><th class="text-right">Avg Cost</th><th class="text-right">Valuation</th><th></th></tr></thead>
      <tbody>
        @foreach($stocks as $s)
          <tr class="border-b">
            <td>{{ $s->product->name ?? ('#'.$s->product_id) }}</td>
            <td class="text-right">{{ $s->qty }}</td>
            <td class="text-right">{{ $s->avg_cost }}</td>
            <td class="text-right">{{ number_format((float)$s->qty * (float)$s->avg_cost, 2) }}</td>
            <td class="text-right">
              <a class="underline" href="{{ route('stocks.ledger', $s->product_id) }}">Ledger</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="mt-3">{{ $stocks->links() }}</div>
  </div>
</div>
@endsection
