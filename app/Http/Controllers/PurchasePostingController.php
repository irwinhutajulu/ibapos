<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Services\PurchasePostingService;
use Illuminate\Http\Request;

class PurchasePostingController extends Controller
{
    public function index()
    {
        $purchases = \App\Models\Purchase::with('supplier')->latest('date')->paginate(20);
        return view('purchases.index', compact('purchases'));
    }
    // Route-level middleware is applied in routes/web.php

    public function receive(Request $request, Purchase $purchase, PurchasePostingService $service)
    {
        $service->markAsReceived($purchase, (int)auth()->id());
        return response()->json(['status' => 'ok', 'purchase' => $purchase->fresh('items')]);
    }

    public function post(Request $request, Purchase $purchase, PurchasePostingService $service)
    {
        $service->post($purchase, (int)auth()->id());
        return response()->json(['status' => 'ok', 'purchase' => $purchase->fresh('items')]);
    }

    public function void(Request $request, Purchase $purchase, PurchasePostingService $service)
    {
        $service->void($purchase, (int)auth()->id());
        return response()->json(['status' => 'ok', 'purchase' => $purchase->fresh('items')]);
    }
}
