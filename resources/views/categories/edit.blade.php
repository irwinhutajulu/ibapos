@extends('layouts.app', ['title' => 'Edit Category'])

@section('content')
<form method="post" action="{{ route('categories.update', $category) }}" class="max-w-md bg-white border rounded-md p-4 space-y-3">
  @csrf @method('put')
  <div>
    <label class="block text-sm mb-1">Name</label>
    <input name="name" value="{{ old('name', $category->name) }}" class="w-full px-3 py-2 border rounded-md" required>
    @error('name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
  </div>
  <div class="pt-2 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Save</button>
    <a href="{{ route('categories.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
  </div>
</form>
@endsection
