@extends('layouts.app', ['title' => 'Stock Adjustment'])

@section('content')
<div class="max-w-3xl bg-white border rounded-md p-4">
  <div class="flex items-start justify-between gap-4">
    <div class="flex-1">
      <div class="flex items-center justify-between">
        <div class="font-semibold text-lg">Adjustment #{{ $adjustment->id }} <span class="ml-2 text-xs px-2 py-0.5 rounded bg-gray-100">{{ $adjustment->status }}</span></div>
        <a class="ml-4 text-gray-600 underline" href="{{ route('stock-adjustments.index') }}">Back</a>
      </div>

      <div class="mt-4 grid md:grid-cols-3 gap-3 text-sm">
        <div>
          <div class="text-gray-500">Date</div>
          <div>{{ $adjustment->date }}</div>
        </div>
        <div>
          <div class="text-gray-500">Reason</div>
          <div>{{ $adjustment->reason ?? '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500">Note</div>
          <div>{{ $adjustment->note ?? '-' }}</div>
        </div>
      </div>

      <div class="mt-4 overflow-auto">
        <table class="w-full">
          <thead>
            <tr class="text-left border-b">
              <th>Product</th>
              <th class="text-right">Qty +/-</th>
              <th class="text-right">Unit Cost</th>
              <th>Note</th>
            </tr>
          </thead>
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

      <div class="mt-4 flex items-center gap-2">
        @if($adjustment->status==='draft')
          @can('stocks.adjust')
            <a class="px-3 py-2 border rounded-md" href="{{ route('stock-adjustments.edit', $adjustment) }}">Edit</a>
            <form method="POST" action="{{ route('stock-adjustments.post', $adjustment) }}" class="inline">@csrf<button class="px-3 py-2 bg-green-600 text-white rounded-md">Post</button></form>
          @endcan
        @endif
        @if($adjustment->status==='posted')
          @can('stocks.adjust')
            <form method="POST" action="{{ route('stock-adjustments.void', $adjustment) }}" class="inline">@csrf<button class="px-3 py-2 bg-red-600 text-white rounded-md">Void</button></form>
          @endcan
        @endif
        <a href="{{ route('stock-adjustments.index') }}" class="ml-auto text-gray-600 underline">Back to list</a>
      </div>
    </div>
  </div>
</div>
@endsection
