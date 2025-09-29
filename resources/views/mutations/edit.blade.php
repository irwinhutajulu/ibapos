@extends('layouts.app', ['title' => 'Edit Stock Mutation'])

@section('content')
<div class="max-w-3xl mx-auto">
    @include('mutations._flash_notify')
    <h2 class="text-2xl font-bold mb-4">Edit Mutation #{{ $mutation->id }}</h2>

    <form action="{{ route('stock-mutations.update', $mutation) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Product</label>
                <select name="product_id" class="mt-1 block w-full rounded-md border px-3 py-2 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">-- Select product --</option>
                    @foreach($products as $id => $name)
                        <option value="{{ $id }}" {{ old('product_id', $mutation->product_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">From Location</label>
                    <select name="from_location_id" class="mt-1 block w-full rounded-md border px-3 py-2 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="">-- Select origin --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('from_location_id', $mutation->from_location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">To Location</label>
                    <select name="to_location_id" class="mt-1 block w-full rounded-md border px-3 py-2 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="">-- Select destination --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('to_location_id', $mutation->to_location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" step="0.001" min="0.01" name="qty" value="{{ old('qty', $mutation->qty) }}" class="mt-1 block w-full rounded-md border px-3 py-2 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Note (optional)</label>
                <input type="text" name="note" value="{{ old('note', $mutation->note) }}" class="mt-1 block w-full rounded-md border px-3 py-2 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" maxlength="255" />
            </div>

            <div class="flex justify-end">
                <a href="{{ route('stock-mutations.show', $mutation) }}" class="btn btn-secondary mr-2">Cancel</a>
                <button class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </form>
</div>

@endsection
