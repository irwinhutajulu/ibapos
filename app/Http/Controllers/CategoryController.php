<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        if ($request->has('modal')) {
            return view('categories.create-modal');
        }
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:150|unique:categories,name']);
        $category = Category::create($data);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'category' => $category
            ]);
        }
        
        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }

    public function edit(Category $category, Request $request)
    {
        if ($request->has('modal')) {
            return view('categories.edit-modal', compact('category'));
        }
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate(['name' => 'required|string|max:150|unique:categories,name,'.$category->id]);
        $category->update($data);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'category' => $category
            ]);
        }
        
        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Request $request, Category $category)
    {
        $categoryName = $category->name;
        $category->delete();
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Category '{$categoryName}' deleted successfully!"
            ]);
        }
        
        return redirect()->route('categories.index')->with('success', "Category '{$categoryName}' deleted successfully!");
    }
}
