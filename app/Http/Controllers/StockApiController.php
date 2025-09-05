<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockApiController extends Controller
{
    public function available(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'location_id' => 'nullable|integer',
        ]);
        $locationId = $validated['location_id'] ?? (int) session('active_location_id');
        $onHand = (float) Stock::where('product_id', $validated['product_id'])->where('location_id', $locationId)->value('qty') ?? 0.0;
        $reserved = (float) StockReservation::where('product_id', $validated['product_id'])->where('location_id', $locationId)->where('status','active')->sum('qty_reserved');
        return response()->json(['available' => max(0, $onHand - $reserved)]);
    }

    public function availableBatch(Request $request)
    {
        $items = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.location_id' => 'nullable|integer',
        ])['items'];

        $active = (int) session('active_location_id');
        $result = [];

        foreach ($items as $k => $row) {
            $pid = (int) $row['product_id'];
            $lid = (int) ($row['location_id'] ?? $active);
            $onHand = (float) Stock::where('product_id', $pid)->where('location_id', $lid)->value('qty') ?? 0.0;
            $reserved = (float) StockReservation::where('product_id', $pid)->where('location_id', $lid)->where('status','active')->sum('qty_reserved');
            $result[$k] = [
                'product_id' => $pid,
                'location_id' => $lid,
                'available' => max(0, $onHand - $reserved),
            ];
        }

        return response()->json(['data' => array_values($result)]);
    }
}
