@extends('layouts.app', ['title' => 'Suppliers'])
@section('content')
<div class="bg-white border rounded-md p-4">
  <form method="get" class="mb-4 flex gap-2">
    <input name="q" value="{{ $q }}" class="px-3 py-2 border rounded" placeholder="Search name...">
    <button class="px-3 py-2 bg-blue-600 text-white rounded">Search</button>
    <a href="{{ route('suppliers.create') }}" class="ml-auto px-3 py-2 bg-green-600 text-white rounded">New Supplier</a>
  </form>
  @if(session('ok'))<script>window.notify(@json(session('ok')), 'success')</script>@endif
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="text-left px-3 py-2">Name</th>
        <th class="text-left px-3 py-2">Phone</th>
        <th class="text-left px-3 py-2">Address</th>
        <th class="px-3 py-2">Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($suppliers as $s)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $s->name }}</td>
        <td class="px-3 py-2">{{ $s->phone }}</td>
        <td class="px-3 py-2">{{ $s->address }}</td>
        <td class="px-3 py-2 flex gap-2">
          <a href="{{ route('suppliers.edit', $s) }}" class="text-blue-600">Edit</a>
          @if(!$s->trashed())
          <form method="post" action="{{ route('suppliers.destroy', $s) }}" onsubmit="return confirm('Delete supplier?')">
            @csrf @method('DELETE')
            <button class="text-red-600">Delete</button>
          </form>
          @else
          <form method="post" action="{{ route('suppliers.restore', $s->id) }}">
            @csrf
            <button class="text-green-600">Restore</button>
          </form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $suppliers->links() }}</div>
</div>
@endsection
