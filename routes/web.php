<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('dashboard'); })->middleware('auth');

// Auth
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Posting and inventory operations
Route::middleware(['web','auth'])->group(function () {
    Route::post('/active-location', [\App\Http\Controllers\ActiveLocationController::class, 'set'])->name('active-location.set');
    Route::view('/pos', 'pos.index')->name('pos.index');
    // Sales draft & list
    Route::get('/sales', [\App\Http\Controllers\SalesController::class, 'index'])->middleware('permission:sales.read')->name('sales.index');
    Route::post('/sales', [\App\Http\Controllers\SalesController::class, 'store'])->middleware('permission:sales.create')->name('sales.store');
    // Sales posting
    Route::post('/sales/{sale}/post', [\App\Http\Controllers\SalesPostingController::class, 'post'])->name('sales.post');
    Route::post('/sales/{sale}/void', [\App\Http\Controllers\SalesPostingController::class, 'void'])->name('sales.void');

    // Purchases
    Route::get('/purchases', [\App\Http\Controllers\PurchasePostingController::class, 'index'])->middleware('permission:purchases.read')->name('purchases.index');
    Route::post('/purchases/{purchase}/receive', [\App\Http\Controllers\PurchasePostingController::class, 'receive'])->middleware('permission:purchases.receive')->name('purchases.receive');
    Route::post('/purchases/{purchase}/post', [\App\Http\Controllers\PurchasePostingController::class, 'post'])->middleware('permission:purchases.post')->name('purchases.post');
    Route::post('/purchases/{purchase}/void', [\App\Http\Controllers\PurchasePostingController::class, 'void'])->middleware('permission:purchases.void')->name('purchases.void');

    // Stock mutations
    Route::get('/stock-mutations', [\App\Http\Controllers\StockMutationsController::class, 'index'])->middleware('permission:stock_mutations.request')->name('stock-mutations.index');
    Route::post('/stock-mutations/{mutation}/confirm', [\App\Http\Controllers\StockMutationsController::class, 'confirm'])->middleware('permission:stock_mutations.confirm')->name('stock-mutations.confirm');
    Route::post('/stock-mutations/{mutation}/reject', [\App\Http\Controllers\StockMutationsController::class, 'reject'])->middleware('permission:stock_mutations.reject')->name('stock-mutations.reject');

    // Stock adjustments
    Route::get('/stock-adjustments', [\App\Http\Controllers\StockAdjustmentsController::class, 'index'])->middleware('permission:stocks.read')->name('stock-adjustments.index');
    Route::post('/stock-adjustments/{adjustment}/post', [\App\Http\Controllers\StockAdjustmentsController::class, 'post'])->middleware('permission:stocks.adjust')->name('stock-adjustments.post');
    Route::post('/stock-adjustments/{adjustment}/void', [\App\Http\Controllers\StockAdjustmentsController::class, 'void'])->middleware('permission:stocks.adjust')->name('stock-adjustments.void');

    // Simple APIs for POS
    Route::get('/api/locations', function() {
        $user = auth()->user();
        return $user->locations()->select('id','name')->orderBy('name')->get();
    })->name('api.locations');

    Route::get('/api/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('api.products');
    Route::get('/api/stock/available', [\App\Http\Controllers\StockApiController::class, 'available'])->name('api.stock.available');
    Route::post('/api/stock/available-batch', [\App\Http\Controllers\StockApiController::class, 'availableBatch'])->name('api.stock.available-batch');
});
