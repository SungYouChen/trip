<?php

namespace App\Http\Controllers;

use App\Data\Itinerary;
use Illuminate\Http\Request;

class ItineraryController extends Controller
{
    public function index()
    {
        $days = Itinerary::getAll();
        $flights = Itinerary::getFlightInfo();

        // Calculate totals for each day
        $allExpenses = \App\Models\Expense::all();

        foreach ($days as &$day) {
            // Determine DB date (same logic as show method)
            $parts = explode('/', $day['date']);
            $month = (int)$parts[0];
            $d = (int)$parts[1];
            $year = ($month >= 11) ? 2025 : 2026;

            $dbDate = sprintf('%d-%02d-%02d', $year, $month, $d);

            $day['total_expense'] = $allExpenses->where('date', $dbDate)->sum('amount');
        }
        unset($day); // Break reference

        // Fetch from DB
        $shoppingItems = \App\Models\ChecklistItem::where('type', 'shopping')->get()->groupBy('category');
        $spotItems = \App\Models\ChecklistItem::where('type', 'spot')->get()->groupBy('category');

        return view('index', [
            'days' => $days,
            'flights' => $flights,
            'shoppingList' => $shoppingItems,
            'spotList' => $spotItems
        ]);
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:shopping,spot',
            'category' => 'required|string',
            'name' => 'required|string',
        ]);

        \App\Models\ChecklistItem::create($validated);

        return back()->with('success', '已成功新增項目！');
    }

    public function deleteItem($id)
    {
        \App\Models\ChecklistItem::destroy($id);
        return back()->with('success', '已刪除項目。');
    }

    public function show($date)
    {
        // Decode the date from URL (e.g. 12-28) to match data format (12/28)
        // Actually, let's just make the URL param easy.
        // I'll use the array index for simplicity, or just search by date.

        $targetDate = str_replace('-', '/', $date);
        $days = Itinerary::getAll();


        $dayData = collect($days)->firstWhere('date', $targetDate);

        if (!$dayData) {
            abort(404);
        }

        // Calculate full date for DB query
        // Format: '12/28' -> '2025-12-28', '1/8' -> '2026-01-08'
        $parts = explode('/', $targetDate);
        $month = (int)$parts[0];
        $day = (int)$parts[1];
        $year = ($month >= 11) ? 2025 : 2026; // Assuming trip is Late 2025 - Early 2026

        $dbDate = sprintf('%d-%02d-%02d', $year, $month, $day);

        $expenses = \App\Models\Expense::whereDate('date', $dbDate)->get();

        return view('show', ['day' => $dayData, 'expenses' => $expenses, 'currentDate' => $dbDate]);
    }
}
