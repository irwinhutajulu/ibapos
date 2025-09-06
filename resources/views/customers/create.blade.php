@extends('layouts.app', ['title' => 'New Customer'])
@section('content')
<form method="post" action="{{ route('customers.store') }}" class="bg-white border rounded-md p-4 max-w-lg mx-auto">
  @csrf
  <div class="mb-4">
    <label class="text-sm text-gray-600">Name</label>
    <input name="name" class="w-full px-3 py-2 border rounded" required>
  </div>
  <div class="mb-4">
    <label class="text-sm text-gray-600">Phone</label>
    <input name="phone" class="w-full px-3 py-2 border rounded">
  </div>
  <div class="mb-4">
    <label class="text-sm text-gray-600">Address</label>
    <input name="address" class="w-full px-3 py-2 border rounded">
  </div>
  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    <a href="{{ route('customers.index') }}" class="underline text-gray-600">Cancel</a>
  </div>
</form>
@endsection
