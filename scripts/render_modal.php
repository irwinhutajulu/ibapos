<?php
// Quick helper to render products.partials.show-form for debugging
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\ViewErrorBag;

// Find a product that has a category
// If an id is provided as first CLI arg, use that product id
$productId = isset($argv[1]) ? (int) $argv[1] : null;
if ($productId) {
    $p = Product::where('id', $productId)->with('category')->first();
} else {
    $p = Product::whereNotNull('category_id')->with('category')->first();
}
if (!$p) {
    echo "NO_PRODUCT\n";
    exit(0);
}
$categories = Category::orderBy('name')->pluck('name', 'id');

// Ensure $errors is available to the view when rendering from CLI
app('view')->share('errors', new ViewErrorBag());

echo "FOUND|{$p->id}|cat:{$p->category_id}|catname:" . ($p->category->name ?? 'NO_CAT') . "\n";

echo view('products.partials.show-form', [
    'product' => $p,
    'categories' => $categories,
    'action' => '',
    'method' => 'GET',
    'mode' => 'show'
])->render();
