<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Services\PurchasePostingService;
use Illuminate\Http\Request;

class PurchasePostingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['active.location', 'permission:purchases.receive'])->only('receive');
        $this->middleware(['active.location', 'permission:purchases.post'])->only('post');
        $this->middleware(['active.location', 'permission:purchases.void'])->only('void');
    }

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
