<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Services\AdjustmentService;
use Illuminate\Http\Request;

class StockAdjustmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['active.location', 'permission:stocks.adjust'])->only('post');
        $this->middleware(['active.location', 'permission:stocks.adjust'])->only('void');
    }

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
