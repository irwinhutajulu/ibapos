<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplierCustomerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Api\ProductController;

Route::get('suppliers', [SupplierCustomerController::class, 'suppliers']);
Route::get('locations', [LocationController::class, 'api']);
Route::get('products', [ProductController::class, 'index']);
// ...existing api routes...
// ...existing api routes...
