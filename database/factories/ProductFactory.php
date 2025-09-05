<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'category_id' => null,
            'barcode' => $this->faker->ean13(),
            'price' => 10000,
            'weight' => 0,
            'unit' => 'pcs',
        ];
    }
}
