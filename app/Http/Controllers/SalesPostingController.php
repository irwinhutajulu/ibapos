<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\SalesPostingService;
use Illuminate\Http\Request;

class SalesPostingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['active.location', 'permission:sales.post'])->only('post');
        $this->middleware(['active.location', 'permission:sales.void'])->only('void');
    }

    public function post(Request $request, Sale $sale, SalesPostingService $service)
    {
        $service->post($sale, (int)auth()->id());
        return response()->json(['status' => 'ok', 'sale' => $sale->fresh('items')]);
    }

    public function void(Request $request, Sale $sale, SalesPostingService $service)
    {
        $service->void($sale, (int)auth()->id());
        return response()->json(['status' => 'ok', 'sale' => $sale->fresh('items')]);
    }
}
