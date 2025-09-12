<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplierCustomerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\SalesController;

Route::get('suppliers', [SupplierCustomerController::class, 'suppliers']);
Route::get('locations', [LocationController::class, 'api']);
Route::get('products', [ProductController::class, 'index']);

// Sales API routes
Route::middleware(['auth'])->group(function () {
    Route::get('sales', [SalesController::class, 'index']);
    Route::get('sales/{sale}', [SalesController::class, 'show']);
    Route::delete('sales/{sale}', function(\App\Models\Sale $sale) {
        if ($sale->status === 'posted') {
            return response()->json(['message' => 'Cannot delete posted sales'], 422);
        }
        $sale->delete();
        return response()->json(['message' => 'Sale deleted successfully']);
    });
});

// ...existing api routes...
// ...existing api routes...
