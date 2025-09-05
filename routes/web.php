<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

// Posting and inventory operations
Route::middleware(['web','auth'])->group(function () {
    // Sales posting
    Route::post('/sales/{sale}/post', [\App\Http\Controllers\SalesPostingController::class, 'post'])->name('sales.post');
    Route::post('/sales/{sale}/void', [\App\Http\Controllers\SalesPostingController::class, 'void'])->name('sales.void');

    // Purchases
    Route::post('/purchases/{purchase}/receive', [\App\Http\Controllers\PurchasePostingController::class, 'receive'])->name('purchases.receive');
    Route::post('/purchases/{purchase}/post', [\App\Http\Controllers\PurchasePostingController::class, 'post'])->name('purchases.post');
    Route::post('/purchases/{purchase}/void', [\App\Http\Controllers\PurchasePostingController::class, 'void'])->name('purchases.void');

    // Stock mutations
    Route::post('/stock-mutations/{mutation}/confirm', [\App\Http\Controllers\StockMutationsController::class, 'confirm'])->name('stock-mutations.confirm');
    Route::post('/stock-mutations/{mutation}/reject', [\App\Http\Controllers\StockMutationsController::class, 'reject'])->name('stock-mutations.reject');

    // Stock adjustments
    Route::post('/stock-adjustments/{adjustment}/post', [\App\Http\Controllers\StockAdjustmentsController::class, 'post'])->name('stock-adjustments.post');
    Route::post('/stock-adjustments/{adjustment}/void', [\App\Http\Controllers\StockAdjustmentsController::class, 'void'])->name('stock-adjustments.void');
});
