@extends('layouts.app')
@section('content')
<div class="bg-white border rounded-md">
  <div class="p-3 border-b flex items-center justify-between">
    <div class="font-semibold">Ledger: {{ $product->name }} @if($locationId) <span class="text-xs text-gray-500">(Location #{{ $locationId }})</span> @endif</div>
    <a href="{{ route('stocks.index') }}" class="text-sm underline">Back</a>
  </div>
  <div class="p-3 overflow-auto text-sm">
    <table class="w-full">
      <thead>
        <tr class="text-left border-b">
          <th>Date</th>
          <th>Ref</th>
          <th class="text-right">Qty Δ</th>
          <th class="text-right">Balance</th>
          <th class="text-right">Cost/Unit</th>
          <th class="text-right">Total Cost</th>
          <th>Note</th>
        </tr>
      </thead>
      <tbody>
        @foreach($entries as $e)
          <tr class="border-b">
            <td>{{ $e->created_at }}</td>
            <td>{{ $e->ref_type }} #{{ $e->ref_id }}</td>
            <td class="text-right">{{ $e->qty_change }}</td>
            <td class="text-right">{{ $e->balance_after }}</td>
            <td class="text-right">{{ $e->cost_per_unit_at_time ?? '-' }}</td>
            <td class="text-right">{{ $e->total_cost_effect ?? '-' }}</td>
            <td>{{ $e->note }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="mt-3">{{ $entries->links() }}</div>
  </div>
</div>
@endsection
