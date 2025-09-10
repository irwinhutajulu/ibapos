<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplierCustomerController;

Route::get('/suppliers', [SupplierCustomerController::class, 'suppliers']);
// ...existing api routes...
