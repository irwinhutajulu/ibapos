@extends('layouts.app', ['title' => 'Mutation Detail'])

@section('content')
    @include('mutations._flash_notify')
    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold mb-4">Mutation #{{ $mutation->id }}</h2>

        <div class="bg-white shadow rounded p-4 mb-4 dark:bg-gray-800 dark:shadow-none">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <strong>Product</strong>
                    <div class="text-gray-900 dark:text-gray-100">{{ $mutation->product->name ?? '#'.$mutation->product_id }}</div>
                </div>
                <div>
                    <strong>Quantity</strong>
                    <div class="text-gray-900 dark:text-gray-100">{{ number_format($mutation->qty,2,',','.') }}</div>
                </div>
                <div>
                    <strong>From</strong>
                    <div class="text-gray-900 dark:text-gray-100">{{ $mutation->fromLocation->name ?? 'Location #'.$mutation->from_location_id }}</div>
                </div>
                <div>
                    <strong>To</strong>
                    <div class="text-gray-900 dark:text-gray-100">{{ $mutation->toLocation->name ?? 'Location #'.$mutation->to_location_id }}</div>
                </div>
                <div class="col-span-2">
                    <strong>Note</strong>
                    <div class="text-gray-700 dark:text-gray-300">{{ $mutation->note }}</div>
                </div>
                <div>
                    <strong>Status</strong>
                    <div class="text-gray-900 dark:text-gray-100">{{ ucfirst($mutation->status) }}</div>
                </div>
                <div>
                    <strong>Requested by</strong>
                    <div class="text-gray-900 dark:text-gray-100">{{ optional($mutation->requestedBy)->name ?? 'User #' . $mutation->requested_by }}</div>
                </div>
            </div>
        </div>

        @if($mutation->status === 'pending')
            <div class="flex gap-2">
                <form action="{{ route('stock-mutations.confirm', $mutation) }}" method="POST">
                    @csrf
                    <button class="btn btn-success">Confirm</button>
                </form>

                <form action="{{ route('stock-mutations.reject', $mutation) }}" method="POST">
                    @csrf
                    <button class="btn btn-danger">Reject</button>
                </form>
            </div>
        @endif
    </div>

@endsection
