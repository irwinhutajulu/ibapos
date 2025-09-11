<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('expense_categories.read');
        $categories = ExpenseCategory::orderBy('name')->paginate(20);
        return view('expense_categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        Gate::authorize('expense_categories.create');
        if ($request->get('modal') === '1') {
            return view('expense_categories.partials.create-form');
        }
        return view('expense_categories.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('expense_categories.create');
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:expense_categories,name',
            'description' => 'nullable|string|max:255',
        ]);
        ExpenseCategory::create($validated);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Category created successfully']);
        }
        return redirect()->route('expense_categories.index')->with('success','Category created');
    }

    public function edit(Request $request, ExpenseCategory $expense_category)
    {
        Gate::authorize('expense_categories.update');
        if ($request->get('modal') === '1') {
            return view('expense_categories.partials.edit-form', compact('expense_category'));
        }
        return view('expense_categories.edit', compact('expense_category'));
    }

    public function update(Request $request, ExpenseCategory $expense_category)
    {
        Gate::authorize('expense_categories.update');
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:expense_categories,name,' . $expense_category->id,
            'description' => 'nullable|string|max:255',
        ]);
        $expense_category->update($validated);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Category updated successfully']);
        }
        return redirect()->route('expense_categories.index')->with('success','Category updated');
    }

    public function show(Request $request, ExpenseCategory $expense_category)
    {
        Gate::authorize('expense_categories.read');
        if ($request->get('modal') === '1') {
            return view('expense_categories.partials.show-form', compact('expense_category'));
        }
        return view('expense_categories.show', compact('expense_category'));
    }

    public function destroy(ExpenseCategory $expense_category)
    {
        Gate::authorize('expense_categories.delete');
        $expense_category->delete();
        return redirect()->route('expense_categories.index')->with('success','Category deleted');
    }
}
