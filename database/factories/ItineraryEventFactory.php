<?php

namespace Database\Factories;

use App\Models\ItineraryDay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItineraryEvent>
 */
class ItineraryEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'itinerary_day_id' => ItineraryDay::factory(),
            'time' => '10:00',
            'activity' => '景點參觀',
            'note' => '記得帶相機',
            'map_query' => '東京鐵塔',
            'sub_activities' => ['拍團體照', '買紀念品'],
            'sort_order' => 0,
        ];
    }
}
