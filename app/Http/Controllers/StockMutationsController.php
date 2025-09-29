<?php

namespace App\Http\Controllers;

use App\Models\StockMutation;
use App\Services\MutationService;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Location;

class StockMutationsController extends Controller
{
    public function index()
    {
        $mutations = \App\Models\StockMutation::latest('date')->paginate(20);
        return view('mutations.index', compact('mutations'));
    }
    
    public function create()
    {
        $products = Product::orderBy('name')->pluck('name','id');
        $locations = Location::orderBy('name')->get();
        return view('mutations.create', compact('products','locations'));
    }

    public function store(Request $request, MutationService $service)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'qty' => 'required|numeric|min:0.001',
            'note' => 'nullable|string|max:255',
        ]);

        $mutation = $service->create(
            (int)$data['product_id'],
            (int)$data['from_location_id'],
            (int)$data['to_location_id'],
            (string)$data['qty'],
            (int)auth()->id(),
            $data['note'] ?? null
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok', 'mutation' => $mutation], 201);
        }

        return redirect()->route('stock-mutations.index')->with('success', 'Stock mutation request created.');
    }
    // Route-level middleware is applied in routes/web.php

    public function confirm(Request $request, StockMutation $mutation, MutationService $service)
    {
        $service->confirm($mutation, (int)auth()->id());
        $mutation = $mutation->fresh();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok', 'mutation' => $mutation]);
        }

        return redirect()->back()->with('success', 'Stock mutation confirmed.');
    }

    public function reject(Request $request, StockMutation $mutation, MutationService $service)
    {
        $service->reject($mutation, (int)auth()->id(), $request->string('reason')->toString() ?: null);
        $mutation = $mutation->fresh();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok', 'mutation' => $mutation]);
        }

        return redirect()->back()->with('success', 'Stock mutation rejected.');
    }

    public function show(StockMutation $mutation)
    {
        $mutation->load(['product','fromLocation','toLocation']);
        return view('mutations.show', compact('mutation'));
    }

    public function edit(StockMutation $mutation)
    {
        if ($mutation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending mutations can be edited.');
        }
        $products = Product::orderBy('name')->pluck('name','id');
        $locations = Location::orderBy('name')->get();
        return view('mutations.edit', compact('mutation','products','locations'));
    }

    public function update(Request $request, StockMutation $mutation)
    {
        if ($mutation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending mutations can be updated.');
        }

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'qty' => 'required|numeric|min:0.001',
            'note' => 'nullable|string|max:255',
        ]);

        $mutation->update([
            'product_id' => $data['product_id'],
            'from_location_id' => $data['from_location_id'],
            'to_location_id' => $data['to_location_id'],
            'qty' => $data['qty'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('stock-mutations.show', $mutation)->with('success', 'Mutation updated.');
    }
}
