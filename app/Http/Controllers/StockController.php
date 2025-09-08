<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
        // If this is an AJAX/search request, return JSON in the shape used by the live-search-table component
        if ($request->ajax() || $request->wantsJson()) {
            $rows = $stocks->map(function($s) {
                // Normalize to primitives for the live-search-table template
                $qty = number_format($s->qty, 0);
                $avg = 'Rp ' . number_format($s->avg_cost, 2, ',', '.');
                $valuation = 'Rp ' . number_format((float)$s->qty * (float)$s->avg_cost, 2, ',', '.');

                return [
                    'id' => $s->id,
                    'cells' => [
                        [
                            'type' => 'avatar',
                            'image' => optional($s->product)->image_path ? url('storage/'.optional($s->product)->image_path) : null,
                            'name' => optional($s->product)->name ?? ('#'.$s->product_id),
                            'subtitle' => optional($s->product)->barcode ?? 'No Barcode'
                        ],
                        // Quantity as simple string (component expects primitive)
                        $qty,
                        // Avg cost as string
                        $avg,
                        // Valuation as string (component will use formatted or raw)
                        $valuation
                    ],
                    'actions' => [
                        [
                            'type' => 'link',
                            'url' => route('stocks.ledger', $s->product_id),
                            'label' => 'View Ledger',
                            'style' => 'primary',
                            'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
                        ]
                    ]
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $rows,
                'pagination' => [
                    'total' => $stocks->total(),
                    'per_page' => $stocks->perPage(),
                    'current_page' => $stocks->currentPage(),
                    'last_page' => $stocks->lastPage(),
                    'from' => $stocks->firstItem(),
                    'to' => $stocks->lastItem()
                ]
            ]);
        }

        return view('stocks.index', compact('stocks','q'));
    }

    /**
     * Format a collection of stock-like items into the live-search-table JSON shape.
     * Made public/static to allow unit testing without touching the DB/migrations.
     *
     * @param \Illuminate\Support\Collection|array $stocks
     * @param array|null $pagination
     * @return array
     */
    public static function formatStocksForLiveSearch($stocks, $pagination = null): array
    {
        $col = $stocks instanceof Collection ? $stocks : collect($stocks);

        $rows = $col->map(function($s) {
            $qty = number_format($s->qty, 0);
            $avg = 'Rp ' . number_format($s->avg_cost, 2, ',', '.');
            $valuation = 'Rp ' . number_format((float)$s->qty * (float)$s->avg_cost, 2, ',', '.');

            $product = $s->product ?? null;

            return [
                'id' => $s->id,
                'cells' => [
                    [
                        'type' => 'avatar',
                        'image' => $product && ($product->image_path ?? null) ? '/storage/'.$product->image_path : null,
                        'name' => $product->name ?? ('#'.$s->product_id),
                        'subtitle' => $product->barcode ?? 'No Barcode'
                    ],
                    $qty,
                    $avg,
                    $valuation
                ],
                'actions' => [
                    [
                        'type' => 'link',
                        'url' => '/stocks/'.$s->product_id.'/ledger',
                        'label' => 'View Ledger',
                        'style' => 'primary',
                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
                    ]
                ]
            ];
        })->values();

        return [
            'success' => true,
            'data' => $rows,
            'pagination' => $pagination ?? []
        ];
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
