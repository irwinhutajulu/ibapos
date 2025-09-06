@extends('layouts.app')
@section('content')
<div class="bg-white border rounded-md">
  <div class="p-3 border-b flex items-center justify-between">
    <div class="font-semibold">Stock Adjustments</div>
    @can('stocks.adjust')
    <a href="{{ route('stock-adjustments.create') }}" class="px-3 py-1 border rounded">+ Create</a>
    @endcan
  </div>
  <div class="p-3 overflow-auto">
    <table class="w-full text-sm">
      <thead><tr class="text-left border-b"><th>Date</th><th>Code</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      @foreach($adjustments as $a)
        <tr class="border-b">
          <td><a href="{{ route('stock-adjustments.show',$a) }}" class="underline">{{ $a->date }}</a></td>
          <td><a href="{{ route('stock-adjustments.show',$a) }}" class="underline">{{ $a->code }}</a></td>
          <td>{{ $a->status }}</td>
          <td class="space-x-1">
            @if($a->status==='draft')
            <a href="{{ route('stock-adjustments.edit',$a) }}" class="px-2 py-1 border rounded">Edit</a>
            <form method="POST" action="{{ route('stock-adjustments.post',$a) }}" class="inline">@csrf<button class="px-2 py-1 border rounded">Post</button></form>
            @endif
            @if($a->status==='posted')
            <form method="POST" action="{{ route('stock-adjustments.void',$a) }}" class="inline">@csrf<button class="px-2 py-1 border rounded">Void</button></form>
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <div class="mt-3">{{ $adjustments->links() }}</div>
  </div>
</div>
@endsection
