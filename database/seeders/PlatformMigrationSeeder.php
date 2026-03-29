<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\ItineraryDay;
use App\Models\ItineraryEvent;
use App\Models\Expense;
use App\Models\ChecklistItem;
use App\Data\Itinerary;
use Carbon\Carbon;

class PlatformMigrationSeeder extends Seeder
{
    public function run(): void
    {
        // Clean start to avoid duplicates
        Trip::where('name', '日本關西跨年之旅 2024-2025')->delete();

        // 1. Create the base trip
        $trip = Trip::create([
            'name' => '日本關西跨年之旅 2024-2025',
            'start_date' => '2024-12-28',
            'end_date' => '2025-01-08',
            'cover_image' => 'bg.jpg',
        ]);

        // 2. Migrate Itinerary Data
        $itineraryData = Itinerary::getAll();
        foreach ($itineraryData as $dayData) {
            // Parse date (e.g. "12/28")
            // Assuming 2024 for Dec and 2025 for Jan
            $parts = explode('/', $dayData['date']);
            $month = (int)$parts[0];
            $day = (int)$parts[1];
            $year = ($month >= 11) ? 2024 : 2025;
            $fullDate = Carbon::create($year, $month, $day)->format('Y-m-d');

            $dayRecord = ItineraryDay::create([
                'trip_id' => $trip->id,
                'date' => $fullDate,
                'summary' => $dayData['summary'] ?? null,
                'accommodation' => isset($dayData['accommodation']) ? $dayData['accommodation']['name'] : null,
                'accommodation_details' => $dayData['accommodation'] ?? null,
            ]);

            foreach ($dayData['schedule'] as $index => $eventData) {
                ItineraryEvent::create([
                    'itinerary_day_id' => $dayRecord->id,
                    'time' => $eventData['time'],
                    'activity' => $eventData['activity'],
                    'sub_activities' => $eventData['sub_activities'] ?? null,
                    'note' => $eventData['note'] ?? null,
                    'map_query' => $eventData['map_query'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        // 3. Update existing Expenses
        Expense::whereNull('trip_id')->update(['trip_id' => $trip->id]);

        // 4. Update existing Checklist Items
        ChecklistItem::whereNull('trip_id')->update(['trip_id' => $trip->id]);
    }
}
