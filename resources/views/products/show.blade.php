@extends('layouts.app', ['title' => 'Product Detail'])

@section('content')
<div class="max-w-3xl bg-white border rounded-md p-4">
  <div class="flex items-start gap-4">
    <div>
      @if($product->image_url)
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded border" />
      @else
        <div class="w-32 h-32 rounded bg-gray-100 border"></div>
      @endif
    </div>
    <div class="flex-1">
      <h1 class="text-xl font-semibold">{{ $product->name }}</h1>
      @if($product->deleted_at)
        <div class="text-red-700 text-sm">(deleted)</div>
      @endif
      <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
        <div>
          <div class="text-gray-500">Barcode</div>
          <div>{{ $product->barcode ?? '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500">Category</div>
          <div>{{ $product->category?->name ?? '-' }}</div>
          {{-- Debug helper: show raw category_id for troubleshooting --}}
          <div class="text-xs text-gray-400">cat_id: {{ $product->category_id ?? '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500">Unit</div>
          <div>{{ $product->unit ?? '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500">Price</div>
          <div>{{ $product->price !== null ? number_format($product->price,2) : '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500">Weight</div>
          <div>{{ $product->weight !== null ? number_format($product->weight,3) : '-' }}</div>
        </div>
        <div>
          <div class="text-gray-500">Created</div>
          <div>{{ $product->created_at?->format('Y-m-d H:i') }}</div>
        </div>
      </div>

      <div class="mt-4 flex items-center gap-2 text-sm">
        @can('products.update')
          <a href="{{ route('products.edit', $product) }}" class="px-3 py-2 border rounded-md">Edit</a>
        @endcan
        @can('products.delete')
          @if(!$product->deleted_at)
            <form action="{{ route('products.destroy', $product) }}" method="post" onsubmit="return confirm('Delete this product?')">
              @csrf @method('delete')
              <button class="px-3 py-2 border rounded-md text-red-700">Delete</button>
            </form>
          @else
            <form action="{{ route('products.restore', $product->id) }}" method="post">
              @csrf
              <button class="px-3 py-2 border rounded-md text-green-700">Restore</button>
            </form>
            <form action="{{ route('products.force-delete', $product->id) }}" method="post" onsubmit="return confirm('Permanently delete this product? This cannot be undone')">
              @csrf
              <button class="px-3 py-2 border rounded-md text-red-700">Force delete</button>
            </form>
          @endif
        @endcan
        <a href="{{ route('products.index') }}" class="ml-auto text-gray-600 underline">Back to list</a>
      </div>
    </div>
  </div>
</div>
@endsection
