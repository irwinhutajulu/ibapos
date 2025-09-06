@extends('layouts.app')
@section('content')
<div class="bg-white border rounded-md">
  <div class="p-3 border-b flex items-center justify-between">
    <div class="font-semibold">Adjustment #{{ $adjustment->id }} <span class="ml-2 text-xs px-2 py-0.5 rounded bg-gray-100">{{ $adjustment->status }}</span></div>
    <div class="space-x-2">
      <a class="text-sm underline" href="{{ route('stock-adjustments.index') }}">Back</a>
      @if($adjustment->status==='draft')
        @can('stocks.adjust')
        <a class="px-3 py-1 border rounded text-sm" href="{{ route('stock-adjustments.edit', $adjustment) }}">Edit</a>
        <form method="POST" action="{{ route('stock-adjustments.post', $adjustment) }}" class="inline">@csrf<button class="px-3 py-1 border rounded text-sm">Post</button></form>
        @endcan
      @endif
    </div>
  </div>
  <div class="p-3 text-sm">
    <div class="grid md:grid-cols-3 gap-3 mb-4">
      <div><div class="text-gray-500">Date</div><div>{{ $adjustment->date }}</div></div>
      <div><div class="text-gray-500">Reason</div><div>{{ $adjustment->reason ?? '-' }}</div></div>
      <div><div class="text-gray-500">Note</div><div>{{ $adjustment->note ?? '-' }}</div></div>
    </div>
    <div class="overflow-auto">
      <table class="w-full">
        <thead><tr class="text-left border-b"><th>Product</th><th class="text-right">Qty +/-</th><th class="text-right">Unit Cost</th><th>Note</th></tr></thead>
        <tbody>
          @foreach($adjustment->items as $it)
          <tr class="border-b">
            <td>{{ $it->product->name ?? ('#'.$it->product_id) }}</td>
            <td class="text-right">{{ $it->qty_change }}</td>
            <td class="text-right">{{ $it->unit_cost ?? '-' }}</td>
            <td>{{ $it->note }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
