<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->string('q'));
        $products = Product::query()
            ->when($q, fn($b) => $b->where('name', 'like', "%$q%")
                                    ->orWhere('barcode', 'like', "%$q%"))
            ->select('id','name','barcode','price')
            ->orderBy('name')
            ->limit(20)
            ->get();
        return response()->json($products);
    }
}
