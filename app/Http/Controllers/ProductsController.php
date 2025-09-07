<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $categoryId = $request->input('category_id');
        $trashed = $request->boolean('trashed');
        $query = Product::query();
        if ($trashed) { $query->withTrashed(); }
        $products = $query
            ->when($q, fn($b) => $b->where(function($x) use ($q){
                $x->where('name','like',"%$q%")
                  ->orWhere('barcode','like',"%$q%");
            }))
            ->when($categoryId, fn($b) => $b->where('category_id', $categoryId))
            ->with('category:id,name')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();
        $categories = Category::orderBy('name')->pluck('name','id');
        return view('products.index', compact('products','q','trashed','categories','categoryId'));
    }

    public function create(Request $request)
    {
        $categories = Category::orderBy('name')->pluck('name','id');
        
        // If this is a modal request, return only the form
        if ($request->get('modal') === '1') {
            return view('products.partials.create-form', compact('categories'));
        }
        
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => 'nullable|string|max:64|unique:products,barcode',
            'price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:20',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        Product::create($data);
        
        // If this is an AJAX request, return JSON response
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Product '{$data['name']}' created successfully"]);
        }
        
        return redirect()->route('products.index')->with('ok', "Product '{$data['name']}' created successfully");
    }

    public function edit(Request $request, Product $product)
    {
        $categories = Category::orderBy('name')->pluck('name','id');
        
        // If this is a modal request, return only the form
        if ($request->get('modal') === '1') {
            return view('products.partials.edit-form', compact('product', 'categories'));
        }
        
        return view('products.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'sku' => 'nullable|string|max:100|unique:products,sku,'.$product->id,
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => 'nullable|string|max:64|unique:products,barcode,'.$product->id,
            'price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:20',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_image') && $product->image_path) {
            Storage::disk('public')->delete($product->image_path);
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        $product->update($data);
        
        // If this is an AJAX request, return JSON response
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Product '{$data['name']}' updated successfully"]);
        }
        
        return redirect()->route('products.index')->with('ok', "Product '{$data['name']}' updated successfully");
    }

    public function destroy(Product $product)
    {
        $productName = $product->name;
        $product->delete();
        return redirect()->route('products.index')->with('ok', "Product '{$productName}' deleted successfully");
    }

    public function show(Request $request, Product $product)
    {
        $product->load('category:id,name');
        
        // If this is a modal request, return only the form
        if ($request->get('modal') === '1') {
            return view('products.partials.show-form', compact('product'));
        }
        
        return view('products.show', compact('product'));
    }

    public function restore(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $productName = $product->name;
        $product->restore();
        return redirect()->route('products.index', ['trashed' => 1])->with('ok', "Product '{$productName}' restored successfully");
    }

    public function forceDelete(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $productName = $product->name;
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->forceDelete();
        return redirect()->route('products.index', ['trashed' => 1])->with('ok', "Product '{$productName}' permanently deleted");
    }
}
