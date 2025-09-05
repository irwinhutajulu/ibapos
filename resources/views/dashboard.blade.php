@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="p-4 bg-white border rounded-lg">
        <div class="text-sm text-gray-500">Today Sales</div>
        <div class="mt-2 text-2xl font-semibold">Rp 0</div>
    </div>
    <div class="p-4 bg-white border rounded-lg">
        <div class="text-sm text-gray-500">Transactions</div>
        <div class="mt-2 text-2xl font-semibold">0</div>
    </div>
    <div class="p-4 bg-white border rounded-lg">
        <div class="text-sm text-gray-500">Top Product</div>
        <div class="mt-2 text-2xl font-semibold">-</div>
    </div>
    <div class="p-4 bg-white border rounded-lg">
        <div class="text-sm text-gray-500">Stock Alerts</div>
        <div class="mt-2 text-2xl font-semibold">0</div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-4">
    <div class="p-4 bg-white border rounded-lg h-64 flex items-center justify-center text-gray-500">Sales Chart</div>
    <div class="p-4 bg-white border rounded-lg h-64 flex items-center justify-center text-gray-500">Recent Orders</div>
    
</div>
@endsection
