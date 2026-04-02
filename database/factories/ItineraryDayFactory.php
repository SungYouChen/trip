<?php

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItineraryDay>
 */
class ItineraryDayFactory extends Factory
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
            'date' => now()->format('Y-m-d'),
            'title' => '探索市中心',
            'location' => $this->faker->city,
            'summary' => '充實的一天。',
            'accommodation' => '格蘭飯店',
            'accommodation_details' => [
                'name' => '格蘭飯店',
                'address' => '東京都港區',
                'price' => 'JPY 15,000',
                'check_in' => '15:00',
                'note' => '含早餐',
            ],
        ];
    }
}
