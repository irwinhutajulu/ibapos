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

    public function create()
    {
        $categories = Category::orderBy('name')->pluck('name','id');
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
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
        return redirect()->route('products.index')->with('ok','Product created');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->pluck('name','id');
        return view('products.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
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
        return redirect()->route('products.index')->with('ok','Product updated');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('ok','Product deleted');
    }

    public function show(Product $product)
    {
        $product->load('category:id,name');
        return view('products.show', compact('product'));
    }

    public function restore(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->route('products.index', ['trashed' => 1])->with('ok','Product restored');
    }

    public function forceDelete(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->forceDelete();
        return redirect()->route('products.index', ['trashed' => 1])->with('ok','Product permanently deleted');
    }
}
