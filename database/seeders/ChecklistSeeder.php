<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shopping = \App\Data\Itinerary::getShoppingList();
        foreach ($shopping as $category => $items) {
            foreach ($items as $item) {
                \App\Models\ChecklistItem::create([
                    'type' => 'shopping',
                    'category' => $category,
                    'name' => $item
                ]);
            }
        }

        $spots = \App\Data\Itinerary::getSpotList();
        foreach ($spots as $category => $items) {
            foreach ($items as $item) {
                \App\Models\ChecklistItem::create([
                    'type' => 'spot',
                    'category' => $category,
                    'name' => $item
                ]);
            }
        }
    }
}
