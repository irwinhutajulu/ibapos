<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Delivery;
use App\Models\Location;
use App\Models\User;

class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->bothify('DLV-####'),
            'date' => now(),
            'location_id' => Location::factory(),
            'sale_id' => null,
            'status' => 'pending',
            'assigned_to' => User::factory(),
        ];
    }
}
