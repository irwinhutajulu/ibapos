<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kasbon;
use App\Models\User;
use App\Models\Location;

class KasbonFactory extends Factory
{
    protected $model = Kasbon::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->bothify('KSB-####'),
            'user_id' => User::factory(),
            'location_id' => Location::factory(),
            'date' => now()->toDateString(),
            'amount' => $this->faker->randomFloat(2, 1, 500),
            'status' => 'requested',
            'approved_by' => null,
        ];
    }
}
