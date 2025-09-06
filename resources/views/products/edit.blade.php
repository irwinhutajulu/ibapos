@extends('layouts.app', ['title' => 'Edit Product'])

@section('content')
<form method="post" action="{{ route('products.update', $product) }}" class="max-w-xl bg-white border rounded-md p-4 space-y-3" enctype="multipart/form-data">
  @csrf @method('put')
  <div>
    <label class="block text-sm mb-1">Name</label>
    <input name="name" value="{{ old('name', $product->name) }}" class="w-full px-3 py-2 border rounded-md" required>
    @error('name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm mb-1">Category</label>
    <select name="category_id" class="w-full px-3 py-2 border rounded-md">
      <option value="">-</option>
      @foreach($categories as $id=>$name)
        <option value="{{ $id }}" @selected(old('category_id', $product->category_id)==$id)>{{ $name }}</option>
      @endforeach
    </select>
  </div>
  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="block text-sm mb-1">Barcode</label>
      <input name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full px-3 py-2 border rounded-md">
    </div>
    <div>
      <label class="block text-sm mb-1">Unit</label>
      <input name="unit" value="{{ old('unit', $product->unit) }}" class="w-full px-3 py-2 border rounded-md">
    </div>
  </div>
  <div class="space-y-2">
    <label class="block text-sm">Image</label>
    @if($product->image_url)
      <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-20 w-20 object-cover rounded border">
      <div class="flex items-center gap-3 text-sm">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="remove_image" value="1">
          <span>Remove current image</span>
        </label>
        <span class="text-gray-400">or upload a new one:</span>
      </div>
    @endif
    <input type="file" name="image" accept="image/*" class="block w-full text-sm" />
    @error('image')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
  </div>
  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="block text-sm mb-1">Price</label>
      <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" class="w-full px-3 py-2 border rounded-md">
    </div>
    <div>
      <label class="block text-sm mb-1">Weight</label>
      <input type="number" step="0.001" min="0" name="weight" value="{{ old('weight', $product->weight) }}" class="w-full px-3 py-2 border rounded-md">
    </div>
  </div>
  <div class="pt-2 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Save</button>
    <a href="{{ route('products.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
  </div>
</form>
@endsection
