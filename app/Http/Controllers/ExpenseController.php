<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('expenses.read');
        $expenses = Expense::with(['category','location','user'])
            ->orderByDesc('date')
            ->paginate(20);
        return view('expenses.index', compact('expenses'));
    }

    public function create(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('expenses.create');
        $categories = ExpenseCategory::all();
        if ($request->get('modal') === '1') {
            return view('expenses.partials.create-form', compact('categories'));
        }
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('expenses.create');
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);
        $validated['location_id'] = Auth::user()->active_location_id;
        $validated['user_id'] = Auth::id();
        $expense = Expense::create($validated);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Expense created successfully']);
        }
        return redirect()->route('expenses.index')->with('success','Expense created');
    }

    public function edit(Request $request, Expense $expense)
    {
        \Illuminate\Support\Facades\Gate::authorize('expenses.update');
        $categories = ExpenseCategory::all();
        if ($request->get('modal') === '1') {
            return view('expenses.partials.edit-form', compact('expense','categories'));
        }
        return view('expenses.edit', compact('expense','categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        \Illuminate\Support\Facades\Gate::authorize('expenses.update');
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);
        $expense->update($validated);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Expense updated successfully']);
        }
        return redirect()->route('expenses.index')->with('success','Expense updated');
    }
    public function show(Request $request, Expense $expense)
    {
        \Illuminate\Support\Facades\Gate::authorize('expenses.read');
        $expense->load(['category','location','user']);
        if ($request->get('modal') === '1') {
            return view('expenses.partials.show-form', compact('expense'));
        }
        return view('expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
    \Illuminate\Support\Facades\Gate::authorize('expenses.delete');
        $expense->delete();
        return redirect()->route('expenses.index')->with('success','Expense deleted');
    }
}
