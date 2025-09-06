@extends('layouts.app', ['title' => 'Products'])

@section('content')
<div class="flex items-center justify-between mb-4">
  <form method="get" class="flex gap-2 items-center">
    <input name="q" value="{{ $q ?? '' }}" placeholder="Search name/barcode" class="px-3 py-2 border rounded-md text-sm"/>
    <select name="category_id" class="px-2 py-2 border rounded-md text-sm">
      <option value="">All categories</option>
      @foreach(($categories ?? []) as $id=>$name)
        <option value="{{ $id }}" @selected(($categoryId ?? null)==$id)>{{ $name }}</option>
      @endforeach
    </select>
    <button class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm">Search</button>
  </form>
  @can('products.create')
  <a href="{{ route('products.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm">Add Product</a>
  @endcan
  </div>

@if(session('ok'))
  <div class="mb-3 p-2 bg-green-50 border border-green-200 text-green-800 text-sm rounded">{{ session('ok') }}</div>
@endif

@cannot('products.create')
  <div class="mb-2 text-xs text-gray-600">You don’t have permission to create products. Ask an admin to grant “products.create”.</div>
@endcannot

<div class="flex items-center gap-3 mb-2 text-sm">
  <label class="inline-flex items-center gap-1">
    <input type="checkbox" onchange="const u=new URL(window.location);u.searchParams.set('trashed', this.checked?1:''); window.location=u.toString();" @checked($trashed ?? false)>
    Show deleted
  </label>
</div>

<div class="overflow-x-auto bg-white border rounded-md">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="text-left px-3 py-2">Product</th>
        <th class="text-left px-3 py-2">Barcode</th>
        <th class="text-left px-3 py-2">Category</th>
        <th class="text-left px-3 py-2">Price</th>
        <th class="px-3 py-2"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $p)
      <tr class="border-t">
        <td class="px-3 py-2">
          <div class="flex items-center gap-2">
            @if($p->image_url)
              <img src="{{ $p->image_url }}" class="w-10 h-10 rounded object-cover" alt="img" />
            @else
              <div class="w-10 h-10 rounded bg-gray-100 border"></div>
            @endif
            <a href="{{ route('products.show', $p) }}" class="underline">{{ $p->name }}</a>
            @if($p->deleted_at)
              <span class="ml-2 text-xs text-red-700">(deleted)</span>
            @endif
          </div>
        </td>
        <td class="px-3 py-2">{{ $p->barcode }}</td>
        <td class="px-3 py-2">{{ $p->category->name ?? '-' }}</td>
        <td class="px-3 py-2">{{ number_format($p->price,2) }}</td>
        <td class="px-3 py-2 text-right">
          @if(!$p->deleted_at)
            @can('products.update')
            <a class="text-blue-600 hover:underline" href="{{ route('products.edit', $p) }}">Edit</a>
            @endcan
            @can('products.delete')
            <form action="{{ route('products.destroy', $p) }}" method="post" class="inline" onsubmit="return confirm('Delete this product?')">
              @csrf @method('delete')
              <button class="text-red-600 hover:underline ml-2">Delete</button>
            </form>
            @endcan
          @else
            <form action="{{ route('products.restore', $p->id) }}" method="post" class="inline">
              @csrf
              <button class="text-green-700 hover:underline">Restore</button>
            </form>
            <form action="{{ route('products.force-delete', $p->id) }}" method="post" class="inline ml-2" onsubmit="return confirm('Permanently delete this product? This cannot be undone')">
              @csrf
              <button class="text-red-700 hover:underline">Force delete</button>
            </form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $products->links() }}</div>
@endsection
