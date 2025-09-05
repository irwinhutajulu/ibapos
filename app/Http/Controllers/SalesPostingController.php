<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\SalesPostingService;
use Illuminate\Http\Request;

class SalesPostingController extends Controller
{
    // Route-level middleware is applied in routes/web.php

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
