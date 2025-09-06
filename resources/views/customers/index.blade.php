@extends('layouts.app', ['title' => 'Customers'])
@section('content')
<div class="bg-white border rounded-md p-4">
  <form method="get" class="mb-4 flex gap-2">
    <input name="q" value="{{ $q }}" class="px-3 py-2 border rounded" placeholder="Search name...">
    <button class="px-3 py-2 bg-blue-600 text-white rounded">Search</button>
    <a href="{{ route('customers.create') }}" class="ml-auto px-3 py-2 bg-green-600 text-white rounded">New Customer</a>
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
      @foreach($customers as $c)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $c->name }}</td>
        <td class="px-3 py-2">{{ $c->phone }}</td>
        <td class="px-3 py-2">{{ $c->address }}</td>
        <td class="px-3 py-2 flex gap-2">
          <a href="{{ route('customers.edit', $c) }}" class="text-blue-600">Edit</a>
          @if(!$c->trashed())
          <form method="post" action="{{ route('customers.destroy', $c) }}" onsubmit="return confirm('Delete customer?')">
            @csrf @method('DELETE')
            <button class="text-red-600">Delete</button>
          </form>
          @else
          <form method="post" action="{{ route('customers.restore', $c->id) }}">
            @csrf
            <button class="text-green-600">Restore</button>
          </form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $customers->links() }}</div>
</div>
@endsection
