<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('dashboard'); })->middleware('auth');

// Auth
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Posting and inventory operations
Route::middleware(['web','auth'])->group(function () {
    // Categories
    Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index'])->middleware('permission:categories.read')->name('categories.index');
    Route::get('/categories/create', [\App\Http\Controllers\CategoryController::class, 'create'])->middleware('permission:categories.create')->name('categories.create');
    Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store'])->middleware('permission:categories.create')->name('categories.store');
    Route::get('/categories/{category}/edit', [\App\Http\Controllers\CategoryController::class, 'edit'])->middleware('permission:categories.update')->name('categories.edit');
    Route::put('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'update'])->middleware('permission:categories.update')->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'destroy'])->middleware('permission:categories.delete')->name('categories.destroy');

    // Products
    Route::get('/products', [\App\Http\Controllers\ProductsController::class, 'index'])->middleware('permission:products.read')->name('products.index');
    Route::get('/products/create', [\App\Http\Controllers\ProductsController::class, 'create'])->middleware('permission:products.create')->name('products.create');
    Route::post('/products', [\App\Http\Controllers\ProductsController::class, 'store'])->middleware('permission:products.create')->name('products.store');
    Route::get('/products/{product}', [\App\Http\Controllers\ProductsController::class, 'show'])->middleware('permission:products.read')->name('products.show');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductsController::class, 'edit'])->middleware('permission:products.update')->name('products.edit');
    Route::put('/products/{product}', [\App\Http\Controllers\ProductsController::class, 'update'])->middleware('permission:products.update')->name('products.update');
    Route::delete('/products/{product}', [\App\Http\Controllers\ProductsController::class, 'destroy'])->middleware('permission:products.delete')->name('products.destroy');
    Route::post('/products/{id}/restore', [\App\Http\Controllers\ProductsController::class, 'restore'])->middleware('permission:products.delete')->name('products.restore');
    Route::post('/products/{id}/force-delete', [\App\Http\Controllers\ProductsController::class, 'forceDelete'])->middleware('permission:products.delete')->name('products.force-delete');
    Route::post('/active-location', [\App\Http\Controllers\ActiveLocationController::class, 'set'])->name('active-location.set');
    Route::view('/pos', 'pos.index')->name('pos.index');
    // Sales draft & list
    Route::get('/sales', [\App\Http\Controllers\SalesController::class, 'index'])->middleware('permission:sales.read')->name('sales.index');
    Route::get('/sales/{sale}', [\App\Http\Controllers\SalesController::class, 'show'])->middleware('permission:sales.read')->name('sales.show');
    Route::post('/sales', [\App\Http\Controllers\SalesController::class, 'store'])->middleware('permission:sales.create')->name('sales.store');
    // Sales posting
    Route::post('/sales/{sale}/post', [\App\Http\Controllers\SalesPostingController::class, 'post'])->name('sales.post');
    Route::post('/sales/{sale}/void', [\App\Http\Controllers\SalesPostingController::class, 'void'])->name('sales.void');

    // Purchases
    Route::get('/purchases', [\App\Http\Controllers\PurchasePostingController::class, 'index'])->middleware('permission:purchases.read')->name('purchases.index');
    Route::get('/purchases/create', [\App\Http\Controllers\PurchasesController::class, 'create'])->middleware('permission:purchases.create')->name('purchases.create');
    Route::post('/purchases', [\App\Http\Controllers\PurchasesController::class, 'store'])->middleware('permission:purchases.create')->name('purchases.store');
    Route::get('/purchases/{purchase}', [\App\Http\Controllers\PurchasePostingController::class, 'show'])->middleware('permission:purchases.read')->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', [\App\Http\Controllers\PurchasesController::class, 'edit'])->middleware('permission:purchases.update')->name('purchases.edit');
    Route::put('/purchases/{purchase}', [\App\Http\Controllers\PurchasesController::class, 'update'])->middleware('permission:purchases.update')->name('purchases.update');
    Route::post('/purchases/{purchase}/receive', [\App\Http\Controllers\PurchasePostingController::class, 'receive'])->middleware('permission:purchases.receive')->name('purchases.receive');
    Route::post('/purchases/{purchase}/post', [\App\Http\Controllers\PurchasePostingController::class, 'post'])->middleware('permission:purchases.post')->name('purchases.post');
    Route::post('/purchases/{purchase}/void', [\App\Http\Controllers\PurchasePostingController::class, 'void'])->middleware('permission:purchases.void')->name('purchases.void');

    // Stock mutations
    Route::get('/stock-mutations', [\App\Http\Controllers\StockMutationsController::class, 'index'])->middleware('permission:stock_mutations.request')->name('stock-mutations.index');
    Route::post('/stock-mutations/{mutation}/confirm', [\App\Http\Controllers\StockMutationsController::class, 'confirm'])->middleware('permission:stock_mutations.confirm')->name('stock-mutations.confirm');
    Route::post('/stock-mutations/{mutation}/reject', [\App\Http\Controllers\StockMutationsController::class, 'reject'])->middleware('permission:stock_mutations.reject')->name('stock-mutations.reject');

    // Stock adjustments
    Route::get('/stock-adjustments', [\App\Http\Controllers\StockAdjustmentsController::class, 'index'])->middleware('permission:stocks.read')->name('stock-adjustments.index');
    Route::get('/stock-adjustments/create', [\App\Http\Controllers\StockAdjustmentsController::class, 'create'])->middleware('permission:stocks.adjust')->name('stock-adjustments.create');
    Route::post('/stock-adjustments', [\App\Http\Controllers\StockAdjustmentsController::class, 'store'])->middleware('permission:stocks.adjust')->name('stock-adjustments.store');
    Route::get('/stock-adjustments/{adjustment}', [\App\Http\Controllers\StockAdjustmentsController::class, 'show'])->middleware('permission:stocks.read')->name('stock-adjustments.show');
    Route::get('/stock-adjustments/{adjustment}/edit', [\App\Http\Controllers\StockAdjustmentsController::class, 'edit'])->middleware('permission:stocks.adjust')->name('stock-adjustments.edit');
    Route::put('/stock-adjustments/{adjustment}', [\App\Http\Controllers\StockAdjustmentsController::class, 'update'])->middleware('permission:stocks.adjust')->name('stock-adjustments.update');
    Route::post('/stock-adjustments/{adjustment}/post', [\App\Http\Controllers\StockAdjustmentsController::class, 'post'])->middleware('permission:stocks.adjust')->name('stock-adjustments.post');
    Route::post('/stock-adjustments/{adjustment}/void', [\App\Http\Controllers\StockAdjustmentsController::class, 'void'])->middleware('permission:stocks.adjust')->name('stock-adjustments.void');

    // Stock overview and ledger
    Route::get('/stocks', [\App\Http\Controllers\StockController::class, 'index'])->middleware('permission:stocks.read')->name('stocks.index');
    Route::get('/stocks/{product}/ledger', [\App\Http\Controllers\StockController::class, 'ledger'])->middleware('permission:stocks.read')->name('stocks.ledger');

    // Simple APIs for POS
    Route::get('/api/locations', function() {
        $user = auth()->user();
        return $user->locations()->select('id','name')->orderBy('name')->get();
    })->name('api.locations');

    Route::get('/api/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('api.products');
    Route::get('/api/stock/available', [\App\Http\Controllers\StockApiController::class, 'available'])->name('api.stock.available');
    Route::post('/api/stock/available-batch', [\App\Http\Controllers\StockApiController::class, 'availableBatch'])->name('api.stock.available-batch');

    // Reservations UI (optional)
    Route::get('/reservations', [\App\Http\Controllers\ReservationController::class, 'index'])->middleware('permission:stocks.read')->name('reservations.index');
    Route::post('/reservations/{reservation}/release', [\App\Http\Controllers\ReservationController::class, 'release'])->middleware('permission:stocks.adjust')->name('reservations.release');
    Route::post('/reservations/{reservation}/consume', [\App\Http\Controllers\ReservationController::class, 'consume'])->middleware('permission:stocks.adjust')->name('reservations.consume');
    Route::post('/reservations/cleanup-expired', [\App\Http\Controllers\ReservationController::class, 'cleanupExpired'])->middleware('permission:stocks.adjust')->name('reservations.cleanup');

    // Notifications (admin/user inbox)
    Route::get('/admin/notifications', [\App\Http\Controllers\NotificationsController::class, 'index'])->middleware('permission:admin.permissions')->name('admin.notifications.index');
    Route::post('/admin/notifications/{notification}/read', [\App\Http\Controllers\NotificationsController::class, 'markAsRead'])->middleware('permission:admin.permissions')->name('admin.notifications.read');

    // Realtime debug helpers
    Route::view('/debug/realtime', 'debug.realtime')->name('debug.realtime');
    Route::post('/debug/fire', function() {
        $locId = (int) (session('active_location_id') ?? 1);
        event(new \App\Events\StockUpdated(
            productId: 999,
            locationId: $locId,
            qty: 1,
            avgCost: null,
            refType: 'debug',
            refId: 0,
        ));
        return response()->json(['ok' => true, 'location_id' => $locId]);
    })->name('debug.fire');

    Route::post('/debug/sale-posted', function() {
        $locId = (int) (session('active_location_id') ?? 1);
        $sale = new \App\Models\Sale([
            'invoice_no' => 'DBG-POST',
            'location_id' => $locId,
            'status' => 'posted',
            'total' => '123.45',
        ]);
        $sale->id = 99901; // fake id for debug
        $sale->posted_at = now();
        event(new \App\Events\SalePosted($sale));
        return response()->json(['ok' => true]);
    })->name('debug.sale-posted');

    Route::post('/debug/sale-voided', function() {
        $locId = (int) (session('active_location_id') ?? 1);
        $sale = new \App\Models\Sale([
            'invoice_no' => 'DBG-VOID',
            'location_id' => $locId,
            'status' => 'void',
            'total' => '0',
        ]);
        $sale->id = 99902; // fake id for debug
        $sale->voided_at = now();
        event(new \App\Events\SaleVoided($sale));
        return response()->json(['ok' => true]);
    })->name('debug.sale-voided');
});
