<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Trip;
use App\Models\ItineraryDay;
use App\Models\ItineraryEvent;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

class ItineraryDayController extends Controller
{
    public function show(User $user, Trip $trip, $date)
    {
        $data = $this->getDayData($trip, $date);
        $data['user'] = $user;
        $data['isShared'] = false;
        return view('show', $data);
    }

    public function showShared($token, $date)
    {
        $trip = Trip::where('share_token', $token)->where('is_public', true)->firstOrFail();
        $data = $this->getDayData($trip, $date);
        $data['isShared'] = true;
        $data['shareToken'] = $token;
        $data['user'] = $trip->user; // Pass owner
        return view('show', $data);
    }

    private function getDayData(Trip $trip, $date)
    {
        $parts = explode('-', $date);
        $month = (int)$parts[0];
        $dayNum = (int)$parts[1];

        /** @var ItineraryDay $day */
        $day = $trip->days()
            ->whereMonth('date', $month)
            ->whereDay('date', $dayNum)
            ->firstOrFail();

        $day->load(['events' => function ($q) {
            $q->withTrashed()->orderBy('sort_order'); }]);
        $expenses = Expense::withTrashed()
            ->where(fn ($q) => $q->where('trip_id', $trip->id))
            ->whereDate('date', $day->date)
            ->get();

        return [
            'trip' => $trip,
            'day' => [
                'id' => $day->id,
                'date' => Carbon::parse($day->date)->format('n/j'),
                'day' => Carbon::parse($day->date)->locale('zh_TW')->dayName,
                'title' => $day->title ?? $day->summary,
                'summary' => $day->summary,
                'location' => $day->location ?? '',
                'accommodation' => $day->accommodation_details,
                'schedule' => $day->events->sortBy('sort_order')->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'time' => $event->time,
                        'activity' => $event->activity,
                        'sub_activities' => $event->sub_activities,
                        'note' => $event->note,
                        'map_query' => $event->map_query,
                        'trashed' => $event->trashed(),
                    ];
                })->toArray(),
            ],
            'expenses' => $expenses,
            'currentDate' => $day->date,
        ];
    }

    public function updateDay(User $user, Trip $trip, $date, Request $request)
    {
        $parts = explode('-', $date);
        $day = $trip->days()->whereMonth('date', $parts[0])->whereDay('date', $parts[1])->firstOrFail();

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
            'hotel_name' => 'nullable|string|max:255',
            'hotel_address' => 'nullable|string|max:255',
            'hotel_currency' => 'nullable|string|max:20',
            'hotel_price_num' => 'nullable|numeric',
            'hotel_checkin' => 'nullable|string|max:255',
            'hotel_note' => 'nullable|string|max:1000',
        ]);

        $hotelPriceCombined = null;
        if (!empty($validated['hotel_price_num'])) {
            $curr = $validated['hotel_currency'] ?? '$';
            $hotelPriceCombined = $curr . ' ' . number_format((float)$validated['hotel_price_num']);
        }

        $day->update([
            'title' => $validated['title'] ?? null,
            'location' => $validated['location'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'accommodation' => $validated['hotel_name'] ?? null,
            'accommodation_details' => [
                'name' => $validated['hotel_name'] ?? null,
                'address' => $validated['hotel_address'] ?? null,
                'price' => $hotelPriceCombined,
                'check_in' => $validated['hotel_checkin'] ?? null,
                'note' => $validated['hotel_note'] ?? null,
            ]
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => '日誌摘要已更新！']);
        }

        return back()->with('success', '日誌摘要已更新！');
    }

    public function addEvent(User $user, Trip $trip, $date, Request $request)
    {
        $parts = explode('-', $date);
        $day = $trip->days()->whereMonth('date', $parts[0])->whereDay('date', $parts[1])->firstOrFail();

        $validated = $request->validate([
            'time' => 'required|string',
            'activity' => 'required|string',
            'note' => 'nullable|string',
            'map_query' => 'nullable|string',
            'sub_activities' => 'nullable|string', // Will be comma separated
        ]);

        $subActivities = ($validated['sub_activities'] ?? null)
            ? array_map('trim', explode(',', $validated['sub_activities']))
            : null;

        $day->events()->create([
            'time' => $validated['time'],
            'activity' => $validated['activity'],
            'note' => $validated['note'] ?? null,
            'map_query' => $validated['map_query'] ?? null,
            'sub_activities' => $subActivities,
            'sort_order' => $day->events()->count(),
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => '行程活動已新增！']);
        }

        return back()->with('success', '行程活動已新增！');
    }

    public function updateEvent(User $user, ItineraryEvent $event, Request $request)
    {
        $validated = $request->validate([
            'time' => 'required|string',
            'activity' => 'required|string',
            'note' => 'nullable|string',
            'map_query' => 'nullable|string',
            'sub_activities' => 'nullable|string',
        ]);

        $subActivities = ($validated['sub_activities'] ?? null)
            ? array_map('trim', explode(',', $validated['sub_activities']))
            : null;

        $event->update([
            'time' => $validated['time'],
            'activity' => $validated['activity'],
            'note' => $validated['note'] ?? null,
            'map_query' => $validated['map_query'] ?? null,
            'sub_activities' => $subActivities,
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => '行程活動已更新！']);
        }

        return back()->with('success', '行程活動已更新！');
    }

    public function deleteEvent(User $user, ItineraryEvent $event, Request $request)
    {
        $event->delete(); // soft delete
        if ($request->ajax()) {
            return response()->json(['message' => '行程活動已封存。']);
        }
        return back()->with('success', '行程活動已封存。');
    }

    public function restoreEvent(User $user, $eventId, Request $request)
    {
        $event = ItineraryEvent::withTrashed()->findOrFail($eventId);
        $event->restore();
        if ($request->ajax()) {
            return response()->json(['message' => '行程活動已還原！']);
        }
        return back()->with('success', '行程活動已還原！');
    }

    public function forceDeleteEvent(User $user, $eventId, Request $request)
    {
        $event = ItineraryEvent::withTrashed()->findOrFail($eventId);
        $event->forceDelete();
        if ($request->ajax()) {
            return response()->json(['message' => '行程活動已永久刪除。']);
        }
        return back()->with('success', '行程活動已永久刪除。');
    }

    public function deleteDay(User $user, Trip $trip, $date, Request $request)
    {
        $parts = explode('-', $date);
        $dayNum = (int)$parts[1];
        $month = (int)$parts[0];

        $day = $trip->days()
            ->whereMonth('date', $month)
            ->whereDay('date', $dayNum)
            ->firstOrFail();

        $day->delete(); // soft delete

        if ($request->ajax()) {
            return response()->json(['message' => '行程卡片已封存。']);
        }
        return back()->with('success', '行程卡片已封存。');
    }

    public function restoreDay(User $user, Trip $trip, $dayId, Request $request)
    {
        $day = ItineraryDay::withTrashed()
            ->where(fn ($q) => $q->where(['id' => $dayId, 'trip_id' => $trip->id]))
            ->firstOrFail();
        $day->restore();
        if ($request->ajax()) {
            return response()->json(['message' => '行程卡片已還原！']);
        }
        return back()->with('success', '行程卡片已還原！');
    }

    public function forceDeleteDay(User $user, Trip $trip, $dayId, Request $request)
    {
        $day = ItineraryDay::withTrashed()
            ->where(fn ($q) => $q->where(['id' => $dayId, 'trip_id' => $trip->id]))
            ->firstOrFail();
        // Cascade: delete all child events
        $day->events()->withTrashed()->forceDelete();
        $day->forceDelete();
        if ($request->ajax()) {
            return response()->json(['message' => '行程卡片及所有活動已永久刪除。']);
        }
        return back()->with('success', '行程卡片及所有活動已永久刪除。');
    }
}
