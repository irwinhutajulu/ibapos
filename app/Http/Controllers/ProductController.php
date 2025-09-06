<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->string('q'));
        $products = Product::query()
            ->when($q, fn($b) => $b->where('name', 'like', "%$q%")
                                    ->orWhere('barcode', 'like', "%$q%"))
            ->select('id','name','barcode')
            ->orderBy('name')
            ->paginate(20);
        return response()->json($products);
    }
}
