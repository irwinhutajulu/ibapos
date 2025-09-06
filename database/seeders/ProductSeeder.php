<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create some categories first if they don't exist
        $electronics = Category::firstOrCreate(['name' => 'Electronics']);
        $fashion = Category::firstOrCreate(['name' => 'Fashion']);
        $books = Category::firstOrCreate(['name' => 'Books']);

        // Create sample products
        $products = [
            [
                'name' => 'Smartphone Samsung Galaxy',
                'category_id' => $electronics->id,
                'barcode' => '1234567890123',
                'price' => 5000000,
                'weight' => 0.2,
                'unit' => 'pcs'
            ],
            [
                'name' => 'Laptop Lenovo ThinkPad',
                'category_id' => $electronics->id,
                'barcode' => '2345678901234',
                'price' => 15000000,
                'weight' => 2.1,
                'unit' => 'pcs'
            ],
            [
                'name' => 'T-Shirt Cotton Premium',
                'category_id' => $fashion->id,
                'barcode' => '3456789012345',
                'price' => 150000,
                'weight' => 0.3,
                'unit' => 'pcs'
            ],
            [
                'name' => 'Programming Book PHP Laravel',
                'category_id' => $books->id,
                'barcode' => '4567890123456',
                'price' => 250000,
                'weight' => 0.5,
                'unit' => 'pcs'
            ],
            [
                'name' => 'Wireless Headphones',
                'category_id' => $electronics->id,
                'barcode' => '5678901234567',
                'price' => 800000,
                'weight' => 0.25,
                'unit' => 'pcs'
            ]
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['barcode' => $productData['barcode']], 
                $productData
            );
        }
    }
}
