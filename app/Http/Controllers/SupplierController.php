<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $suppliers = Supplier::query()
            ->when($q, fn($qr) => $qr->where('name', 'like', "%$q%"))
            ->orderBy('name')
            ->paginate(20);
        return view('suppliers.index', compact('suppliers', 'q'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);
        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('ok', 'Supplier created.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);
        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('ok', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('ok', 'Supplier deleted.');
    }

    public function restore($id)
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);
        $supplier->restore();
        return redirect()->route('suppliers.index')->with('ok', 'Supplier restored.');
    }
}
