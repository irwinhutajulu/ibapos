<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Services\AdjustmentService;
use Illuminate\Http\Request;

class StockAdjustmentsController extends Controller
{
    public function index()
    {
        $adjustments = \App\Models\StockAdjustment::latest('date')->paginate(20);
        return view('adjustments.index', compact('adjustments'));
    }
    // Route-level middleware is applied in routes/web.php

    public function post(Request $request, StockAdjustment $adjustment, AdjustmentService $service)
    {
        $service->post($adjustment, (int)auth()->id());
        return response()->json(['status' => 'ok', 'adjustment' => $adjustment->fresh('items')]);
    }

    public function void(Request $request, StockAdjustment $adjustment, AdjustmentService $service)
    {
        $service->void($adjustment, (int)auth()->id());
        return response()->json(['status' => 'ok', 'adjustment' => $adjustment->fresh('items')]);
    }
}
