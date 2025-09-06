@extends('layouts.app', ['title' => 'Edit Customer'])
@section('content')
<form method="post" action="{{ route('customers.update', $customer) }}" class="bg-white border rounded-md p-4 max-w-lg mx-auto">
  @csrf @method('PUT')
  <div class="mb-4">
    <label class="text-sm text-gray-600">Name</label>
    <input name="name" value="{{ $customer->name }}" class="w-full px-3 py-2 border rounded" required>
  </div>
  <div class="mb-4">
    <label class="text-sm text-gray-600">Phone</label>
    <input name="phone" value="{{ $customer->phone }}" class="w-full px-3 py-2 border rounded">
  </div>
  <div class="mb-4">
    <label class="text-sm text-gray-600">Address</label>
    <input name="address" value="{{ $customer->address }}" class="w-full px-3 py-2 border rounded">
  </div>
  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
    <a href="{{ route('customers.index') }}" class="underline text-gray-600">Cancel</a>
  </div>
</form>
@endsection
