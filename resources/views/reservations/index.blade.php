@extends('layouts.app', ['title' => 'Reservations'])

@section('content')
<div class="flex items-center justify-between mb-4">
  <form method="get" class="flex gap-2">
    <select name="status" class="px-2 py-1 border rounded-md text-sm" onchange="this.form.submit()">
      <option value="">All</option>
      @foreach(['active','consumed','released','expired'] as $st)
        <option value="{{ $st }}" @selected(($status ?? '')===$st)>{{ ucfirst($st) }}</option>
      @endforeach
    </select>
  </form>
  @can('stocks.adjust')
  <form method="post" action="{{ route('reservations.cleanup') }}">
    @csrf
    <button class="px-3 py-2 bg-amber-600 text-white rounded-md text-sm">Cleanup Expired</button>
  </form>
  @endcan
</div>

@if(session('ok'))
  <div class="mb-3 p-2 bg-green-50 border border-green-200 text-green-800 text-sm rounded">{{ session('ok') }}</div>
@endif

<div class="overflow-x-auto bg-white border rounded-md">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="text-left px-3 py-2">Product</th>
        <th class="text-left px-3 py-2">Location</th>
        <th class="text-left px-3 py-2">Qty</th>
        <th class="text-left px-3 py-2">Status</th>
        <th class="text-left px-3 py-2">Expires</th>
        <th class="px-3 py-2 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($reservations as $r)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $r->product_name }}</td>
        <td class="px-3 py-2">{{ $r->location_name }}</td>
        <td class="px-3 py-2">{{ number_format($r->qty_reserved,3) }}</td>
        <td class="px-3 py-2">{{ ucfirst($r->status) }}</td>
        <td class="px-3 py-2">{{ $r->expires_at ? \Carbon\Carbon::parse($r->expires_at)->format('Y-m-d H:i') : '-' }}</td>
        <td class="px-3 py-2 text-right">
          @if($r->status === 'active')
            @can('stocks.adjust')
            <form action="{{ route('reservations.release', $r->id) }}" method="post" class="inline">
              @csrf
              <button class="text-amber-700 hover:underline">Release</button>
            </form>
            <form action="{{ route('reservations.consume', $r->id) }}" method="post" class="inline ml-2">
              @csrf
              <button class="text-blue-700 hover:underline">Consume</button>
            </form>
            @endcan
          @else
            -
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $reservations->links() }}</div>
@endsection
