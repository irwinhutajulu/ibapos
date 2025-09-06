<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $customers = Customer::query()
            ->when($q, fn($qr) => $qr->where('name', 'like', "%$q%"))
            ->orderBy('name')
            ->paginate(20);
        return view('customers.index', compact('customers', 'q'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);
        Customer::create($data);
        return redirect()->route('customers.index')->with('ok', 'Customer created.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);
        $customer->update($data);
        return redirect()->route('customers.index')->with('ok', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('ok', 'Customer deleted.');
    }

    public function restore($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->restore();
        return redirect()->route('customers.index')->with('ok', 'Customer restored.');
    }
}
