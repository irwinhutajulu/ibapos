@extends('layouts.app', ['title' => 'Add Product'])

@section('content')
<form method="post" action="{{ route('products.store') }}" class="max-w-xl bg-white border rounded-md p-4 space-y-3" enctype="multipart/form-data">
  @csrf
  <div>
    <label class="block text-sm mb-1">Name</label>
    <input name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border rounded-md" required>
    @error('name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm mb-1">Category</label>
    <select name="category_id" class="w-full px-3 py-2 border rounded-md">
      <option value="">-</option>
      @foreach($categories as $id=>$name)
        <option value="{{ $id }}" @selected(old('category_id')==$id)>{{ $name }}</option>
      @endforeach
    </select>
  </div>
  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="block text-sm mb-1">Barcode</label>
      <input name="barcode" value="{{ old('barcode') }}" class="w-full px-3 py-2 border rounded-md">
    </div>
    <div>
      <label class="block text-sm mb-1">Unit</label>
      <input name="unit" value="{{ old('unit') }}" class="w-full px-3 py-2 border rounded-md">
    </div>
  </div>
  <div>
    <label class="block text-sm mb-1">Image</label>
    <input type="file" name="image" accept="image/*" class="block w-full text-sm" />
    @error('image')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
  </div>
  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="block text-sm mb-1">Price</label>
      <input type="number" step="0.01" min="0" name="price" value="{{ old('price', 0) }}" class="w-full px-3 py-2 border rounded-md">
    </div>
    <div>
      <label class="block text-sm mb-1">Weight</label>
      <input type="number" step="0.001" min="0" name="weight" value="{{ old('weight') }}" class="w-full px-3 py-2 border rounded-md">
    </div>
  </div>
  <div class="pt-2 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Save</button>
    <a href="{{ route('products.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
  </div>
</form>
@endsection
