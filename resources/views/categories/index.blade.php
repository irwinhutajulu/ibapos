@extends('layouts.app', ['title' => 'Categories'])

@section('content')
<div class="flex items-center justify-between mb-4">
  <div class="font-medium">Categories</div>
  @can('categories.create')
  <a href="{{ route('categories.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm">Add Category</a>
  @endcan
</div>

@if(session('ok'))
  <div class="mb-3 p-2 bg-green-50 border border-green-200 text-green-800 text-sm rounded">{{ session('ok') }}</div>
@endif

<div class="overflow-x-auto bg-white border rounded-md">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="text-left px-3 py-2">Name</th>
        <th class="px-3 py-2 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($categories as $c)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $c->name }}</td>
        <td class="px-3 py-2 text-right">
          @can('categories.update')
          <a class="text-blue-600 hover:underline" href="{{ route('categories.edit', $c) }}">Edit</a>
          @endcan
          @can('categories.delete')
          <form action="{{ route('categories.destroy', $c) }}" method="post" class="inline" onsubmit="return confirm('Delete this category?')">
            @csrf @method('delete')
            <button class="text-red-600 hover:underline ml-2">Delete</button>
          </form>
          @endcan
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $categories->links() }}</div>
@endsection
