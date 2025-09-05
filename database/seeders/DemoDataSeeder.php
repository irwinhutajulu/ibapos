<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
    $loc = Location::first();
    $wh = Location::where('name','Warehouse')->first();
    if (!$loc) { return; }

        $cat = Category::firstOrCreate(['name' => 'Umum']);
        $products = [
            ['name' => 'Produk A', 'price' => 15000],
            ['name' => 'Produk B', 'price' => 25000],
            ['name' => 'Produk C', 'price' => 5000],
        ];
        $created = [];
        foreach ($products as $p) {
            $prod = Product::firstOrCreate(['name' => $p['name']], ['category_id'=>$cat->id,'price'=>$p['price'],'unit'=>'pcs']);
            $created[] = $prod;
            Stock::firstOrCreate(['product_id'=>$prod->id,'location_id'=>$loc->id], ['qty'=>'30','avg_cost'=>'10000']);
            if ($wh) {
                Stock::firstOrCreate(['product_id'=>$prod->id,'location_id'=>$wh->id], ['qty'=>'50','avg_cost'=>'9500']);
            }
        }

        // Simple purchase draft with items (received-ready)
        if (class_exists(\App\Models\Purchase::class)) {
            $supplier = \App\Models\Supplier::firstOrCreate(['name' => 'PT Sumber Makmur']);
            $purchase = \App\Models\Purchase::firstOrCreate(
                ['invoice_no' => 'PO-DEMO-001', 'location_id' => $loc->id],
                [
                    'date' => now(), 'user_id' => \App\Models\User::first()->id ?? 1,
                    'supplier_id' => $supplier->id,
                    'total' => 0, 'status' => 'draft', 'total_weight' => 10, 'freight_cost' => 50000,
                ]
            );
            if ($purchase->items()->count() === 0 && !empty($created)) {
                foreach (array_slice($created,0,2) as $i => $prod) {
                    $qty = 10 + $i * 5; $price = 9000 + $i*500; $sub = $qty * $price;
                    $purchase->items()->create(['product_id'=>$prod->id,'qty'=>$qty,'price'=>$price,'subtotal'=>$sub]);
                    $purchase->increment('total', $sub);
                }
                $purchase->save();
            }
        }

        // A simple adjustment draft
        if (class_exists(\App\Models\StockAdjustment::class)) {
            $adj = \App\Models\StockAdjustment::firstOrCreate(
                ['code' => 'ADJ-DEMO-001', 'location_id' => $loc->id],
                ['date' => now(), 'status' => 'draft', 'user_id' => \App\Models\User::first()->id ?? 1]
            );
        }

        // A pending stock mutation demo
        if ($wh && class_exists(\App\Models\StockMutation::class) && !empty($created)) {
            $requesterId = \App\Models\User::first()->id ?? 1;
            \App\Models\StockMutation::firstOrCreate(
                [
                    'product_id' => $created[0]->id,
                    'from_location_id' => $wh->id,
                    'to_location_id' => $loc->id,
                    'date' => now()->startOfDay(),
                    'qty' => 5,
                    'status' => 'pending',
                ],
                [
                    'requested_by' => $requesterId,
                ]
            );
        }
    }
}
