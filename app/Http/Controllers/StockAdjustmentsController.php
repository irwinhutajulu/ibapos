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

    public function create(Request $request)
    {
        // Simple create form; items entered as dynamic rows in the view
        $locations = \App\Models\Location::orderBy('name')->get(['id','name']);
    $products = \App\Models\Product::orderBy('name')->get(['id','name']);
        return view('adjustments.create', compact('locations', 'products'));
    }

    public function store(Request $request, AdjustmentService $service)
    {
        $validated = $request->validate([
            'reason' => ['nullable','string','max:200'],
            'note' => ['nullable','string','max:500'],
            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['required','integer','exists:products,id'],
            'items.*.qty_change' => ['required','numeric','not_in:0'],
            'items.*.unit_cost' => ['nullable','numeric','min:0'],
            'items.*.note' => ['nullable','string','max:200'],
        ]);

        $locationId = (int) session('active_location_id');
        if (!$locationId) {
            return back()->withErrors(['location' => 'Pilih lokasi aktif terlebih dahulu.']);
        }

        // Filter out empty rows if any
        $items = collect($validated['items'])
            ->filter(fn($it) => isset($it['product_id']) && isset($it['qty_change']) && $it['qty_change'] != 0)
            ->map(function ($it) {
                return [
                    'product_id' => (int)$it['product_id'],
                    'qty_change' => (string)$it['qty_change'],
                    'unit_cost' => isset($it['unit_cost']) && $it['unit_cost'] !== '' ? (string)$it['unit_cost'] : null,
                    'note' => $it['note'] ?? null,
                ];
            })
            ->values()
            ->all();

        if (empty($items)) {
            return back()->withErrors(['items' => 'Minimal satu item diperlukan.'])->withInput();
        }

        $adj = $service->create(
            locationId: $locationId,
            items: $items,
            reason: $validated['reason'] ?? null,
            note: $validated['note'] ?? null,
            userId: (int)auth()->id()
        );

        return redirect()->route('stock-adjustments.index')->with('status', 'Draft penyesuaian stok berhasil dibuat.');
    }

    public function post(Request $request, StockAdjustment $adjustment, AdjustmentService $service)
    {
    $this->authorize('post', $adjustment);
    $service->post($adjustment, (int)auth()->id());
    return response()->json(['status' => 'ok', 'adjustment' => $adjustment->fresh('items')]);
    }

    public function void(Request $request, StockAdjustment $adjustment, AdjustmentService $service)
    {
    $this->authorize('void', $adjustment);
    $service->void($adjustment, (int)auth()->id());
    return response()->json(['status' => 'ok', 'adjustment' => $adjustment->fresh('items')]);
    }

    public function show(StockAdjustment $adjustment)
    {
        $adjustment->load('items.product');
        return view('adjustments.show', compact('adjustment'));
    }

    public function edit(StockAdjustment $adjustment)
    {
        if ($adjustment->status !== 'draft') {
            return redirect()->route('stock-adjustments.show', $adjustment)->with('error', 'Only draft can be edited.');
        }
        $adjustment->load('items.product');
        $locations = \App\Models\Location::orderBy('name')->get(['id','name']);
    $products = \App\Models\Product::orderBy('name')->get(['id','name']);
        return view('adjustments.edit', compact('adjustment', 'locations', 'products'));
    }

    public function update(Request $request, StockAdjustment $adjustment)
    {
        if ($adjustment->status !== 'draft') {
            return redirect()->route('stock-adjustments.show', $adjustment)->with('error', 'Only draft can be updated.');
        }
        $validated = $request->validate([
            'reason' => ['nullable','string','max:200'],
            'note' => ['nullable','string','max:500'],
            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['required','integer','exists:products,id'],
            'items.*.qty_change' => ['required','numeric','not_in:0'],
            'items.*.unit_cost' => ['nullable','numeric','min:0'],
            'items.*.note' => ['nullable','string','max:200'],
        ]);

        $items = collect($validated['items'])
            ->filter(fn($it) => isset($it['product_id']) && isset($it['qty_change']) && $it['qty_change'] != 0)
            ->values();

        $adjustment->update([
            'reason' => $validated['reason'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);
        // Replace items
        $adjustment->items()->delete();
        foreach ($items as $it) {
            $adjustment->items()->create([
                'product_id' => (int)$it['product_id'],
                'qty_change' => (string)$it['qty_change'],
                'unit_cost' => isset($it['unit_cost']) && $it['unit_cost'] !== '' ? (string)$it['unit_cost'] : null,
                'note' => $it['note'] ?? null,
            ]);
        }

        return redirect()->route('stock-adjustments.show', $adjustment)->with('status', 'Draft updated');
    }
}
