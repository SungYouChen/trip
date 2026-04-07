<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Trip;
use App\Models\ItineraryDay;
use App\Models\ItineraryEvent;
use App\Models\Expense;
use App\Models\DayComment;
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

    private function getItineraryDay(Trip $trip, $date)
    {
        if (str_starts_with($date, 'day-')) {
            $dayNum = (int) str_replace('day-', '', $date);
            return $trip->days()->where('day_number', $dayNum)->firstOrFail();
        }

        $parts = explode('-', $date);
        return $trip->days()
            ->whereMonth('date', (int)$parts[0])
            ->whereDay('date', (int)$parts[1])
            ->firstOrFail();
    }

    private function getDayData(Trip $trip, $date)
    {
        /** @var ItineraryDay $day */
        $day = $this->getItineraryDay($trip, $date);

        $day->load(['events' => function ($q) {
            $q->withTrashed()->orderBy('sort_order'); }, 'comments']);
        $expenses = Expense::withTrashed()
            ->where(fn ($q) => $q->where('trip_id', $trip->id))
            ->whereDate('date', $day->date)
            ->get();

        return [
            'trip' => $trip,
            'day' => [
                'id' => $day->id,
                'date' => $day->date ? Carbon::parse($day->date)->format('n/j') : 'Day ' . $day->day_number,
                'date_obj' => $day->date ? Carbon::parse($day->date) : null,
                'day' => $day->date ? Carbon::parse($day->date)->locale('zh_TW')->dayName : '',
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
                        'latitude' => $event->latitude,
                        'longitude' => $event->longitude,
                        'trashed' => $event->trashed(),
                    ];
                })->toArray(),
                'comments' => $day->comments->map(function($comment) use ($trip) {
                    $canDelete = false;
                    if (auth()->check()) {
                        if (auth()->id() === $trip->user_id || auth()->id() === $comment->user_id) {
                            $canDelete = true;
                        }
                    }
                    return [
                        'id' => $comment->id,
                        'user_name' => $comment->user_name ?: '匿名旅伴',
                        'content' => $comment->content,
                        'time' => $comment->created_at->format('n/j H:i'),
                        'can_delete' => $canDelete
                    ];
                })->toArray(),
            ],
            'expenses' => $expenses,
            'currentDate' => $date, // Using the raw $date parameter since $day->date might be null
        ];
    }

    public function updateDay(User $user, Trip $trip, $date, Request $request)
    {
        $day = $this->getItineraryDay($trip, $date);

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
        $day = $this->getItineraryDay($trip, $date);

        $validated = $request->validate([
            'time' => 'required|string',
            'activity' => 'required|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'map_query' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'sub_activities' => 'nullable|string', // Will be comma separated
        ]);

        $subActivities = ($validated['sub_activities'] ?? null)
            ? array_map('trim', explode(',', $validated['sub_activities']))
            : null;

        $day->events()->create([
            'time' => $validated['time'],
            'activity' => $validated['activity'],
            'address' => $validated['address'] ?? null,
            'note' => $validated['note'] ?? null,
            'map_query' => $validated['map_query'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
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
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'map_query' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'sub_activities' => 'nullable|string',
        ]);

        $subActivities = ($validated['sub_activities'] ?? null)
            ? array_map('trim', explode(',', $validated['sub_activities']))
            : null;

        $event->update([
            'time' => $validated['time'],
            'activity' => $validated['activity'],
            'address' => $validated['address'] ?? null,
            'note' => $validated['note'] ?? null,
            'map_query' => $validated['map_query'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
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
        $day = $this->getItineraryDay($trip, $date);

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

    public function addComment($dayId, Request $request)
    {
        $day = ItineraryDay::findOrFail($dayId);
        $request->validate(['content' => 'required|string|max:1000']);
        
        $day->comments()->create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'content' => $request->content
        ]);

        return back()->with('success', '留言成功！');
    }

    public function addCommentShared($dayId, Request $request)
    {
        $day = ItineraryDay::findOrFail($dayId);
        $request->validate([
            'user_name' => 'required|string|max:50',
            'content' => 'required|string|max:1000'
        ]);
        
        $day->comments()->create([
            'user_name' => $request->user_name,
            'content' => $request->content
        ]);

        return back()->with('success', '留言成功！');
    }

    public function deleteComment($commentId)
    {
        $comment = DayComment::findOrFail($commentId);
        $trip = $comment->itineraryDay->trip;

        // AUTH: Trip owner OR Comment author
        if (auth()->check() && (auth()->id() === $trip->user_id || auth()->id() === $comment->user_id)) {
            $comment->delete();
            return back()->with('success', '留言已刪除。');
        }

        return abort(403, '權限不足。');
    }
}
