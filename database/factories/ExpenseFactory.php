<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Location;
use App\Models\User;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition()
    {
        return [
            'category_id' => ExpenseCategory::factory(),
            'location_id' => Location::factory(),
            'user_id' => User::factory(),
            'date' => now()->toDateString(),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'description' => $this->faker->sentence(),
        ];
    }
}
