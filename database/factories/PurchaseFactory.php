<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Location;
use App\Models\Supplier;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition()
    {
        return [
            'invoice_no' => $this->faker->unique()->bothify('INV-####'),
            'date' => now(),
            'user_id' => User::factory(),
            'location_id' => Location::factory(),
            'supplier_id' => Supplier::factory(),
            'total' => $this->faker->randomFloat(2, 1, 1000),
            'total_weight' => $this->faker->randomFloat(3, 0.1, 100),
            'freight_cost' => $this->faker->randomFloat(2, 0, 200),
            'status' => 'draft',
        ];
    }
}
