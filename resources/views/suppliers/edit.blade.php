@extends('layouts.app', ['title' => 'Edit Supplier'])
@section('content')
<form method="post" action="{{ route('suppliers.update', $supplier) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-lg mx-auto">
  @csrf @method('PUT')
  <div class="mb-4">
    <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Name</label>
    <input name="name" value="{{ $supplier->name }}" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white" required>
  </div>
  <div class="mb-4">
    <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Phone</label>
    <input name="phone" value="{{ $supplier->phone }}" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white">
  </div>
  <div class="mb-4">
    <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Address</label>
    <input name="address" value="{{ $supplier->address }}" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white">
  </div>
  <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
    <a href="{{ route('suppliers.index') }}" class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Cancel</a>
    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-2">
      <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      Update
    </button>
  </div>
</form>
@endsection
