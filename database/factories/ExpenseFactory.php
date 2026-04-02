<?php

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'description' => '午餐拉麵',
            'amount' => 1200.00,
            'category' => 'Food',
            'date' => now()->format('Y-m-d'),
            'is_base_currency' => 0,
        ];
    }
}
