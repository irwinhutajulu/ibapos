<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    // Return JSON aggregates for dashboard
    public function dashboard(Request $request)
    {
        $locationId = (int) ($request->get('location_id') ?? session('active_location_id'));

        if (!$locationId) {
            return response()->json(['error' => 'No active location set'], 400);
        }

        // Date range: today by default
        $from = now()->startOfDay();
        $to = now()->endOfDay();

        // Today's sales total and transactions count
        $salesQuery = Sale::query()->where('location_id', $locationId)->where('status', 'posted')
            ->whereBetween('posted_at', [$from, $to]);

        $todayTotal = (float) $salesQuery->sum('total');
        $todayCount = (int) $salesQuery->count();

        // Recent orders (last 5 posted)
        $recent = Sale::query()->where('location_id', $locationId)->where('status', 'posted')
            ->orderByDesc('posted_at')->take(5)
            ->get(['id','invoice_no','total','posted_at']);

        // Top product (aggregate qty sold this month)
        $startMonth = now()->startOfMonth();
        $top = DB::table('sale_items')
            ->join('sales','sale_items.sale_id','=','sales.id')
            ->join('products','sale_items.product_id','=','products.id')
            ->where('sales.location_id', $locationId)
            ->where('sales.status','posted')
            ->whereBetween('sales.posted_at', [$startMonth, $to])
            ->select('products.id','products.name', DB::raw('SUM(sale_items.qty) as qty_sold'))
            ->groupBy('products.id','products.name')
            ->orderByDesc('qty_sold')
            ->limit(1)
            ->first();

        // Stock alerts: count products with qty <= threshold (threshold default = 0)
        $threshold = (int) ($request->get('threshold') ?? 0);
        $alerts = Stock::where('location_id', $locationId)->where('qty','<=',$threshold)->count();

        return response()->json([
            'today_total' => $todayTotal,
            'today_count' => $todayCount,
            'recent' => $recent,
            'top_product' => $top ? ['id'=>$top->id,'name'=>$top->name,'qty_sold'=>(int)$top->qty_sold] : null,
            'stock_alerts' => (int) $alerts,
            'location_id' => $locationId,
        ]);
    }

    // Return time series of sales totals for last N days (default 7)
    public function salesSeries(Request $request)
    {
        $locationId = (int) ($request->get('location_id') ?? session('active_location_id'));
        if (!$locationId) {
            return response()->json(['error' => 'No active location set'], 400);
        }

        $days = (int) max(1, min(90, $request->get('days', 7)));
        $end = now()->endOfDay();
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = DB::table('sales')
            ->select(DB::raw("DATE(posted_at) as day"), DB::raw('COALESCE(SUM(total),0) as total'))
            ->where('location_id', $locationId)
            ->where('status','posted')
            ->whereBetween('posted_at', [$start, $end])
            ->groupBy(DB::raw('DATE(posted_at)'))
            ->orderBy('day')
            ->get()
            ->pluck('total','day')
            ->toArray();

        $labels = [];
        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i)->toDateString();
            $labels[] = $d;
            $data[] = isset($rows[$d]) ? (float)$rows[$d] : 0.0;
        }

        return response()->json(['labels' => $labels, 'data' => $data, 'location_id' => $locationId]);
    }
}
