<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Test routes for development (no auth required)
Route::get('/test/products', function() {
    $products = \App\Models\Product::with('category')->paginate(15);
    $categories = \App\Models\Category::pluck('name', 'id');
    return view('products.index', [
        'products' => $products,
        'categories' => $categories,
        'q' => '',
        'categoryId' => '',
        'trashed' => false
    ]);
})->name('test.products');

Route::get('/test/api/products/search', [\App\Http\Controllers\Api\ProductController::class, 'search'])->name('test.api.products.search');
Route::get('/test/api/debug', [\App\Http\Controllers\Api\TestController::class, 'test'])->name('test.api.debug');

// AJAX Products route (without middleware for testing)
Route::get('/ajax/products', [\App\Http\Controllers\ProductsController::class, 'index'])->name('ajax.products');

Route::get('/', function () { return view('dashboard'); })->middleware('auth');
Route::get('/dashboard', function () { return view('dashboard'); })->middleware('auth')->name('dashboard');

// Notification preferences
Route::middleware(['auth'])->group(function() {
    Route::get('/notifications/preferences', [\App\Http\Controllers\NotificationsController::class, 'preferences'])->name('admin.notifications.preferences');
    Route::post('/notifications/preferences', [\App\Http\Controllers\NotificationsController::class, 'updatePreferences'])->name('admin.notifications.preferences.update');
});

// Test dropdown components
Route::get('/dropdown-test', function () { return view('dropdown-demo'); })->middleware('auth')->name('dropdown.test');

// Direct products endpoint for AJAX - completely isolated, no middleware
Route::get('/api/products/search', function (Request $request) {
    try {
        $search = trim((string) $request->get('search', ''));
        $page = (int) $request->get('page', 1);
        $perPage = 15;
        
        // Simple query without middleware dependencies
        $query = \App\Models\Product::select(['id', 'name', 'barcode', 'category_id', 'price', 'created_at', 'deleted_at'])
            ->when($search, function($q) use ($search) {
                return $q->where(function($x) use ($search) {
                    $x->where('name', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc');
        
        $total = $query->count();
        $products = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        
        // Get category names separately to avoid relation issues
        $categoryIds = $products->pluck('category_id')->unique()->filter();
        $categories = [];
        if ($categoryIds->isNotEmpty()) {
            $categories = \App\Models\Category::whereIn('id', $categoryIds)->pluck('name', 'id')->toArray();
        }
        
        // Add category names to products
        $products->each(function($product) use ($categories) {
            $product->category_name = $categories[$product->category_id] ?? null;
            // Add image_url accessor manually
            $product->image_url = $product->image_path ? asset('storage/' . $product->image_path) : null;
            // Add sku as null since it doesn't exist in this table
            $product->sku = null;
        });
        
        $lastPage = ceil($total / $perPage);
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
                'from' => ($page - 1) * $perPage + 1,
                'to' => min($page * $perPage, $total),
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Products search API error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Search failed: ' . $e->getMessage()
        ], 500);
    }
})->name('api.products.search');

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

    // API Supplier/Customer search (for dropdown)
    Route::get('/api/suppliers', [\App\Http\Controllers\Api\SupplierCustomerController::class, 'suppliers'])->middleware('auth');
    Route::get('/api/customers', [\App\Http\Controllers\Api\SupplierCustomerController::class, 'customers'])->middleware('auth');
    Route::get('/customers', [\App\Http\Controllers\CustomerController::class, 'index'])->middleware('permission:customers.read')->name('customers.index');
    Route::get('/customers/create', [\App\Http\Controllers\CustomerController::class, 'create'])->middleware('permission:customers.create')->name('customers.create');
    Route::post('/customers', [\App\Http\Controllers\CustomerController::class, 'store'])->middleware('permission:customers.create')->name('customers.store');
    Route::get('/customers/{customer}/edit', [\App\Http\Controllers\CustomerController::class, 'edit'])->middleware('permission:customers.update')->name('customers.edit');
    Route::put('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'update'])->middleware('permission:customers.update')->name('customers.update');
    Route::delete('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'destroy'])->middleware('permission:customers.delete')->name('customers.destroy');
    Route::post('/customers/{id}/restore', [\App\Http\Controllers\CustomerController::class, 'restore'])->middleware('permission:customers.delete')->name('customers.restore');
    Route::get('/suppliers', [\App\Http\Controllers\SupplierController::class, 'index'])->middleware('permission:suppliers.read')->name('suppliers.index');
    Route::get('/suppliers/create', [\App\Http\Controllers\SupplierController::class, 'create'])->middleware('permission:suppliers.create')->name('suppliers.create');
    Route::post('/suppliers', [\App\Http\Controllers\SupplierController::class, 'store'])->middleware('permission:suppliers.create')->name('suppliers.store');
    Route::get('/suppliers/{supplier}/edit', [\App\Http\Controllers\SupplierController::class, 'edit'])->middleware('permission:suppliers.update')->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'update'])->middleware('permission:suppliers.update')->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'destroy'])->middleware('permission:suppliers.delete')->name('suppliers.destroy');
    Route::post('/suppliers/{id}/restore', [\App\Http\Controllers\SupplierController::class, 'restore'])->middleware('permission:suppliers.delete')->name('suppliers.restore');
    
    // Locations
    Route::get('/locations', [\App\Http\Controllers\LocationController::class, 'index'])->middleware('permission:admin.locations')->name('locations.index');
    Route::get('/locations/create', [\App\Http\Controllers\LocationController::class, 'create'])->middleware('permission:admin.locations')->name('locations.create');
    Route::post('/locations', [\App\Http\Controllers\LocationController::class, 'store'])->middleware('permission:admin.locations')->name('locations.store');
    Route::get('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'show'])->middleware('permission:admin.locations')->name('locations.show');
    Route::get('/locations/{location}/edit', [\App\Http\Controllers\LocationController::class, 'edit'])->middleware('permission:admin.locations')->name('locations.edit');
    Route::put('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'update'])->middleware('permission:admin.locations')->name('locations.update');
    Route::delete('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'destroy'])->middleware('permission:admin.locations')->name('locations.destroy');

    // Users (Admin)
    Route::get('/admin/users', [\App\Http\Controllers\UserController::class, 'index'])->middleware('permission:admin.users')->name('admin.users.index');
    Route::get('/admin/users/create', [\App\Http\Controllers\UserController::class, 'create'])->middleware('permission:admin.users')->name('admin.users.create');
    Route::post('/admin/users', [\App\Http\Controllers\UserController::class, 'store'])->middleware('permission:admin.users')->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->middleware('permission:admin.users')->name('admin.users.edit');
    Route::put('/admin/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->middleware('permission:admin.users')->name('admin.users.update');
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->middleware('permission:admin.users')->name('admin.users.destroy');
    Route::post('/admin/users/{id}/restore', [\App\Http\Controllers\UserController::class, 'restore'])->middleware('permission:admin.users')->name('admin.users.restore');
    
    Route::get('/products', [\App\Http\Controllers\ProductsController::class, 'index'])->middleware('permission:products.read')->name('products.index');
    Route::get('/products/create', [\App\Http\Controllers\ProductsController::class, 'create'])->middleware('permission:products.create')->name('products.create');
    Route::post('/products', [\App\Http\Controllers\ProductsController::class, 'store'])->middleware('permission:products.create')->name('products.store');
    Route::get('/products/{product}', [\App\Http\Controllers\ProductsController::class, 'show'])->middleware('permission:products.read')->name('products.show');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductsController::class, 'edit'])->middleware('permission:products.update')->name('products.edit');
    Route::put('/products/{product}', [\App\Http\Controllers\ProductsController::class, 'update'])->middleware('permission:products.update')->name('products.update');
    Route::delete('/products/{product}', [\App\Http\Controllers\ProductsController::class, 'destroy'])->middleware('permission:products.delete')->name('products.destroy');
    Route::post('/products/{id}/restore', [\App\Http\Controllers\ProductsController::class, 'restore'])->middleware('permission:products.delete')->name('products.restore');
        // Role management
        Route::get('/admin/roles', [\App\Http\Controllers\RoleController::class, 'index'])->middleware('permission:admin.roles')->name('roles.index');
        Route::get('/admin/roles/create', [\App\Http\Controllers\RoleController::class, 'create'])->middleware('permission:admin.roles')->name('roles.create');
        Route::post('/admin/roles', [\App\Http\Controllers\RoleController::class, 'store'])->middleware('permission:admin.roles')->name('roles.store');
        Route::get('/admin/roles/{role}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])->middleware('permission:admin.roles')->name('roles.edit');
        Route::put('/admin/roles/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->middleware('permission:admin.roles')->name('roles.update');
        Route::delete('/admin/roles/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->middleware('permission:admin.roles')->name('roles.destroy');

        // Permission management
        Route::get('/admin/permissions', [\App\Http\Controllers\PermissionController::class, 'index'])->middleware('permission:admin.permissions')->name('permissions.index');
        Route::get('/admin/permissions/create', [\App\Http\Controllers\PermissionController::class, 'create'])->middleware('permission:admin.permissions')->name('permissions.create');
        Route::post('/admin/permissions', [\App\Http\Controllers\PermissionController::class, 'store'])->middleware('permission:admin.permissions')->name('permissions.store');
        Route::get('/admin/permissions/{permission}/edit', [\App\Http\Controllers\PermissionController::class, 'edit'])->middleware('permission:admin.permissions')->name('permissions.edit');
        Route::put('/admin/permissions/{permission}', [\App\Http\Controllers\PermissionController::class, 'update'])->middleware('permission:admin.permissions')->name('permissions.update');
        Route::delete('/admin/permissions/{permission}', [\App\Http\Controllers\PermissionController::class, 'destroy'])->middleware('permission:admin.permissions')->name('permissions.destroy');
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

    // Admin API for all locations (for location management)
    Route::get('/api/admin/locations', [\App\Http\Controllers\LocationController::class, 'api'])->middleware('permission:admin.locations')->name('api.admin.locations');

    Route::get('/api/products', [\App\Http\Controllers\Api\ProductController::class, 'index'])->name('api.products');
    // Note: /api/products/search is already defined as closure above (line 26) - no duplicate needed
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
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationsController::class, 'markAllRead'])->name('admin.notifications.mark-all-read');

    // Expense Management
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class)
        ->middleware(['permission:expenses.read|expenses.create|expenses.update|expenses.delete','active.location']);

    // Expense Category Management
    Route::resource('expense_categories', App\Http\Controllers\ExpenseCategoryController::class)
        ->middleware(['permission:expense_categories.read|expense_categories.create|expense_categories.update|expense_categories.delete']);
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

    // Kasbon Management
    Route::resource('kasbons', App\Http\Controllers\KasbonController::class)
        ->middleware(['permission:kasbons.read|kasbons.create|kasbons.update|kasbons.delete','active.location']);
});
