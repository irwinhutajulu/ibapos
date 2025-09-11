@extends('layouts.app')

@section('title','Edit Expense')
@section('content')
<div class="p-6 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Expense</h1>
    <form action="{{ route('expenses.update', $expense) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="date" class="block font-semibold mb-1 text-gray-700 dark:text-gray-200">Date</label>
            <input type="date" name="date" id="date" class="form-input w-full" value="{{ old('date', $expense->date->format('Y-m-d')) }}" required>
        </div>
        <div class="mb-4">
            <label for="category_id" class="block font-semibold mb-1 text-gray-700 dark:text-gray-200">Category</label>
            <select name="category_id" id="category_id" class="form-select w-full" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $expense->category_id)==$cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="amount" class="block font-semibold mb-1 text-gray-700 dark:text-gray-200">Amount</label>
            <input type="number" name="amount" id="amount" class="form-input w-full" step="0.01" min="0" value="{{ old('amount', $expense->amount) }}" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block font-semibold mb-1 text-gray-700 dark:text-gray-200">Description</label>
            <input type="text" name="description" id="description" class="form-input w-full" maxlength="255" value="{{ old('description', $expense->description) }}">
        </div>
        <div class="flex gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">Update</button>
            <a href="{{ route('expenses.index') }}" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 px-4 py-2 rounded-lg transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
