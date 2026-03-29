<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateFlightDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trip = \App\Models\Trip::where('name', '日本關西跨年之旅 2024-2025')->first();
        if ($trip) {
            $flightData = \App\Data\Itinerary::getFlightInfo();
            $trip->update(['flight_info' => $flightData]);
        }
    }
}
