<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PurchasesController extends Controller
{
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get(['id','name']);
        $products = Product::orderBy('name')->get(['id','name','weight']);
        $locations = \App\Models\Location::orderBy('name')->get(['id','name']);
        return view('purchases.create', compact('suppliers','products','locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_no' => 'required|string|max:50',
            'date' => 'required|date',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'location_id' => 'required|integer|exists:locations,id',
            'freight_cost' => 'nullable|numeric|min:0',
            'loading_cost' => 'nullable|numeric|min:0',
            'unloading_cost' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $locationId = (int) $data['location_id'];

        $purchase = DB::transaction(function () use ($data, $locationId) {
            // compute totals
            $total = 0.0; $totalWeight = 0.0;
            $weights = Product::whereIn('id', collect($data['items'])->pluck('product_id'))->pluck('weight','id');

            foreach ($data['items'] as &$row) {
                $row['subtotal'] = (float)$row['qty'] * (float)$row['price'];
                $total += $row['subtotal'];
                $w = (float) ($weights[$row['product_id']] ?? 0);
                $totalWeight += ((float)$row['qty']) * $w;
            }
            // break reference to avoid unexpected side-effects when re-iterating $data['items']
            unset($row);

            $purchase = Purchase::create([
                'invoice_no' => $data['invoice_no'],
                'date' => $data['date'],
                'user_id' => auth()->id(),
                'location_id' => $locationId,
                'supplier_id' => $data['supplier_id'],
                'total' => $total,
                'total_weight' => $totalWeight,
                'freight_cost' => $data['freight_cost'] ?? 0,
                    'loading_cost' => $data['loading_cost'] ?? 0,
                    'unloading_cost' => $data['unloading_cost'] ?? 0,
                'status' => 'draft',
            ]);

            $items = [];
            foreach ($data['items'] as $row) {
                $items[] = new PurchaseItem([
                    'product_id' => $row['product_id'],
                    'qty' => $row['qty'],
                    'price' => $row['price'],
                    'subtotal' => $row['subtotal'],
                ]);
            }
            $purchase->items()->saveMany($items);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)->with('ok', 'Purchase created.');
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->status !== 'draft') {
            throw new BadRequestHttpException('Only draft purchases can be edited.');
        }
        $purchase->load('items');
        $suppliers = Supplier::orderBy('name')->get(['id','name']);
        $products = Product::orderBy('name')->get(['id','name','weight']);
        return view('purchases.edit', compact('purchase','suppliers','products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status !== 'draft') {
            throw new BadRequestHttpException('Only draft purchases can be updated.');
        }

        $data = $request->validate([
            'invoice_no' => 'required|string|max:50',
            'date' => 'required|date',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'freight_cost' => 'nullable|numeric|min:0',
            'loading_cost' => 'nullable|numeric|min:0',
            'unloading_cost' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $purchase) {
            $total = 0.0; $totalWeight = 0.0;
            $weights = Product::whereIn('id', collect($data['items'])->pluck('product_id'))->pluck('weight','id');
            foreach ($data['items'] as &$row) {
                $row['subtotal'] = (float)$row['qty'] * (float)$row['price'];
                $total += $row['subtotal'];
                $w = (float) ($weights[$row['product_id']] ?? 0);
                $totalWeight += ((float)$row['qty']) * $w;
            }
            // break reference to avoid unexpected side-effects when re-iterating $data['items']
            unset($row);

            $purchase->update([
                'invoice_no' => $data['invoice_no'],
                'date' => $data['date'],
                'supplier_id' => $data['supplier_id'],
                'freight_cost' => $data['freight_cost'] ?? 0,
                'loading_cost' => $data['loading_cost'] ?? 0,
                'unloading_cost' => $data['unloading_cost'] ?? 0,
                'total' => $total,
                'total_weight' => $totalWeight,
            ]);

            // replace items
            $purchase->items()->delete();
            $items = [];
            foreach ($data['items'] as $row) {
                $items[] = new PurchaseItem([
                    'product_id' => $row['product_id'],
                    'qty' => $row['qty'],
                    'price' => $row['price'],
                    'subtotal' => $row['subtotal'],
                ]);
            }
            $purchase->items()->saveMany($items);
        });

        return redirect()->route('purchases.show', $purchase)->with('ok', 'Purchase updated.');
    }
}
