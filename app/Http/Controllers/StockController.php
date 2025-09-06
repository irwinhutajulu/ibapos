<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockLedger;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->string('q'));
        $stocks = Stock::with('product')
            ->when($q, fn($b) => $b->whereHas('product', fn($p) => $p->where('name','like',"%$q%")
                                                                  ->orWhere('barcode','like',"%$q%")))
            ->orderByDesc('qty')
            ->paginate(20);
        return view('stocks.index', compact('stocks','q'));
    }

    public function ledger(Product $product, Request $request)
    {
        $locationId = (int) session('active_location_id');
        $entries = StockLedger::where('product_id', $product->id)
            ->when($locationId, fn($b)=>$b->where('location_id', $locationId))
            ->orderByDesc('created_at')
            ->paginate(50);
        return view('stocks.ledger', compact('product','entries','locationId'));
    }
}
