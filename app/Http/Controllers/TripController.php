<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class TripController extends Controller
{
    public function index(User $user)
    {
        $trips = Trip::withTrashed()
            ->where(fn($q) => $q->where('user_id', $user->id))
            ->orderBy('start_date', 'desc')
            ->get();
            
        return view('trips.index', compact('trips', 'user'));
    }

    public function show(User $user, Trip $trip)
    {
        $data = $this->getTripShowData($trip);
        $data['isShared'] = false;
        $data['user'] = $user;
        return view('trips.show', $data);
    }

    public function addItem(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:shopping,spot',
            'category' => 'required|string',
            'name' => 'required|string',
        ]);

        $trip->checklistItems()->create($validated);

        return back()->with('success', 'Item added!');
    }

    public function deleteItem(User $user, Trip $trip, $id)
    {
        $trip->checklistItems()->findOrFail($id)->delete(); // soft delete
        return back()->with('success', '清單項目已封存。');
    }

    public function restoreItem(User $user, Trip $trip, $id)
    {
        $item = $trip->checklistItems()->withTrashed()->findOrFail($id);
        $item->restore();
        return back()->with('success', '清單項目已還原！');
    }

    public function forceDeleteItem(User $user, Trip $trip, $id)
    {
        $item = $trip->checklistItems()->withTrashed()->findOrFail($id);
        $item->forceDelete();
        return back()->with('success', '清單項目已永久刪除。');
    }

    public function store(User $user, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'base_currency' => 'required|string|max:10',
            'target_currency' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'transport_type' => 'nullable|string|in:flight,train,bus,car,ship',
        ]);

        // Validate image separately to give more control
        if ($request->hasFile('cover_image')) {
            $imageValidated = $request->validate([
                'cover_image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            ]);
        }

        $coverPath = null;
        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['cover_image'] = $coverPath;
        $validated['flight_info'] = [
            'transport_type' => $request->transport_type ?? 'flight',
            'airline' => '',
            'price' => '',
            'baggage' => '',
            'outbound' => ['date' => '', 'time' => '', 'route' => ''],
            'inbound' => ['date' => '', 'time' => '', 'route' => '']
        ];
        
        $trip = Trip::create($validated);

        // Auto-generate days
        $start = \Carbon\Carbon::parse($trip->start_date);
        $end = \Carbon\Carbon::parse($trip->end_date);
        
        while ($start <= $end) {
            $trip->days()->create([
                'date' => $start->toDateString(),
                'summary' => '今日尚未安排摘要...',
                'accommodation' => null,
                'accommodation_details' => []
            ]);
            $start->addDay();
        }

        return redirect()->route('trip.show', ['user' => $user, 'trip' => $trip])->with('success', '旅程已建立，已為您預建行程表！');
    }
    public function addDay(User $user, Trip $trip)
    {
        $lastDay = $trip->days()->orderBy('date', 'desc')->first();
        
        if ($lastDay) {
            $newDate = \Carbon\Carbon::parse($lastDay->date)->addDay();
        } else {
            $newDate = \Carbon\Carbon::parse($trip->start_date);
        }

        $trip->days()->create([
            'date' => $newDate->toDateString(),
            'summary' => '今日尚未安排摘要...',
            'accommodation' => null,
            'accommodation_details' => []
        ]);

        return back()->with('success', '已新增一天的行程卡片！');
    }

    public function destroy(User $user, Trip $trip)
    {
        if ($trip->user_id !== auth()->id()) {
            return back()->with('error', '只有旅程建立者可以封存此旅程。');
        }
        $trip->delete(); // soft delete
        return redirect()->route('home', ['user' => auth()->user()])->with('success', '旅程已封存。可在「查看封存」中還原。');
    }

    public function restore(User $user, $tripId)
    {
        $trip = Trip::withTrashed()
            ->where(fn($q) => $q->where('id', $tripId))
            ->firstOrFail();
        $trip->restore();
        return redirect()->route('home', ['user' => $user])->with('success', '旅程已還原！');
    }

    public function forceDelete(User $user, $tripId)
    {
        $trip = Trip::withTrashed()->where(fn($q) => $q->where('id', $tripId)->where('user_id', auth()->id()))->firstOrFail();
        if ($trip->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($trip->cover_image);
        }
        // Cascade: delete all child records
        foreach ($trip->days()->withTrashed()->get() as $day) {
            $day->events()->withTrashed()->forceDelete();
            $day->forceDelete();
        }
        $trip->expenses()->withTrashed()->forceDelete();
        $trip->checklistItems()->withTrashed()->forceDelete();
        $trip->forceDelete();
        return redirect()->route('home', ['user' => $user])->with('success', '旅程及所有子項目已永久刪除。');
    }

    public function updateFlight(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'transport_type' => 'nullable|string|in:flight,train,bus,car,ship',
            'airline' => 'nullable|string|max:100',
            'train_no' => 'nullable|string|max:100',
            'train_seat' => 'nullable|string|max:100',
            'car_model' => 'nullable|string|max:100',
            'flight_price_num' => 'nullable|numeric|min:0',
            'flight_currency' => 'nullable|string|max:10',
            'baggage' => 'nullable|string|max:255',
            'outbound_date' => 'nullable|string|max:50',
            'outbound_time' => 'nullable|string|max:100',
            'outbound_route' => 'nullable|string|max:100',
            'inbound_date' => 'nullable|string|max:50',
            'inbound_time' => 'nullable|string|max:100',
            'inbound_route' => 'nullable|string|max:100',
        ]);

        $currency = $validated['flight_currency'] ?? $trip->base_currency;
        $num = $validated['flight_price_num'] ?? 0;
        $priceStr = $currency . ' ' . number_format($num);

        $flightInfo = [
            'transport_type' => $validated['transport_type'] ?? 'flight',
            'airline' => $validated['airline'] ?? '',
            'train_no' => $validated['train_no'] ?? '',
            'train_seat' => $validated['train_seat'] ?? '',
            'car_model' => $validated['car_model'] ?? '',
            'price' => $num > 0 ? $priceStr : 'TBA',
            'baggage' => $validated['baggage'] ?? '',
            'outbound' => [
                'date' => $validated['outbound_date'] ?? '',
                'time' => $validated['outbound_time'] ?? '',
                'route' => $validated['outbound_route'] ?? ''
            ],
            'inbound' => [
                'date' => $validated['inbound_date'] ?? '',
                'time' => $validated['inbound_time'] ?? '',
                'route' => $validated['inbound_route'] ?? ''
            ]
        ];

        $trip->update(['flight_info' => $flightInfo]);

        return back()->with('success', '交通資訊已更新！');
    }

    public function update(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'base_currency' => 'required|string|max:10',
            'target_currency' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'transport_type' => 'nullable|string|in:flight,train,bus,car,ship',
        ]);

        if ($request->has('restore_cover')) {
            if ($trip->cover_image && \Storage::disk('public')->exists($trip->cover_image)) {
                \Storage::disk('public')->delete($trip->cover_image);
            }
            $trip->cover_image = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($trip->cover_image && \Storage::disk('public')->exists($trip->cover_image)) {
                \Storage::disk('public')->delete($trip->cover_image);
            }
            $path = $request->file('cover_image')->store('covers', 'public');
            $trip->cover_image = $path;
        }

        $flightInfo = $trip->flight_info;
        $flightInfo['transport_type'] = $request->transport_type ?? ($flightInfo['transport_type'] ?? 'flight');

        $trip->update([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'base_currency' => $validated['base_currency'],
            'target_currency' => $validated['target_currency'],
            'exchange_rate' => $validated['exchange_rate'],
            'cover_image' => $trip->cover_image,
            'flight_info' => $flightInfo,
        ]);

        return back()->with('success', '旅程設定與封面已更新！');
    }

    public function toggleShare(User $user, Trip $trip)
    {
        if (!$trip->share_token) {
            $trip->share_token = \Illuminate\Support\Str::random(12);
        }
        $trip->is_public = !$trip->is_public;
        $trip->save();

        $msg = $trip->is_public ? '已開啟分享功能！' : '已關閉分享功能。';
        return back()->with('success', $msg);
    }

    public function indexShared($token)
    {
        /** @var Trip $trip */
        $trip = Trip::where(fn($q) => $q->where('share_token', $token)->where('is_public', true))->firstOrFail();
        
        $data = $this->getTripShowData($trip);
        $data['isShared'] = true;
        $data['user'] = $trip->user; // Pass owner
        return view('trips.show', $data);
    }

    public function addCollaborator(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $collaborator = \App\Models\User::where(fn($q) => $q->where('email', $validated['email']))->first();

        if ($collaborator->id == $trip->user_id) {
            return back()->with('error', '您已經是此行程的擁有者了。');
        }

        $trip->collaborators()->syncWithoutDetaching([$collaborator->id => ['role' => 'editor']]);

        return back()->with('success', '已成功加入協作者！');
    }

    public function updateProfile(User $user, Request $request)
    {
        // 1. Handle Restore Default
        if ($request->has('restore_default')) {
            if ($user->background_image && str_contains($user->background_image, 'backgrounds/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->background_image));
            }
            $user->update([
                'background_image' => null,
                'bg_opacity' => 100,
                'bg_blur' => 0,
                'bg_style' => 'full',
                'bg_width' => 95,
            ]);
            return back()->with('success', '已恢復預設背景與設定！');
        }

        // 2. Handle Settings & Upload
        $validated = $request->validate([
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            'bg_opacity' => 'nullable|integer|min:0|max:100',
            'bg_blur' => 'nullable|integer|min:0|max:20',
            'bg_style' => 'nullable|string|in:full,center',
            'bg_width' => 'nullable|integer|min:0|max:100',
        ]);

        $updateData = [];
        if (isset($validated['bg_opacity'])) $updateData['bg_opacity'] = $validated['bg_opacity'];
        if (isset($validated['bg_blur'])) $updateData['bg_blur'] = $validated['bg_blur'];
        if (isset($validated['bg_style'])) $updateData['bg_style'] = $validated['bg_style'];
        if (isset($validated['bg_width'])) $updateData['bg_width'] = $validated['bg_width'];

        if ($request->hasFile('background_image')) {
            // Delete old one if exists
            if ($user->background_image && str_contains($user->background_image, 'backgrounds/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->background_image));
            }

            $path = $request->file('background_image')->store('backgrounds', 'public');
            $updateData['background_image'] = Storage::url($path);
        }

        if (!empty($updateData)) {
            $user->update($updateData);
            return back()->with('success', '個人設定已更新！');
        }

        return back()->with('info', '未變更任何設定。');
    }

    public function removeCollaborator(User $user, Trip $trip, $collaboratorId)
    {
        $trip->collaborators()->detach($collaboratorId);
        return back()->with('success', '已移除協作者。');
    }

    private function getTripShowData($trip)
    {
        $trip->load(['days' => function ($q) { $q->withTrashed()->orderBy('date'); }, 'collaborators']);
        $checklistItems = $trip->checklistItems()->withTrashed()->get();
        $groupedChecklist = $checklistItems->groupBy('category')->map(function ($items) {
            return $items->mapWithKeys(fn($i) => [$i->id => ['name' => $i->name, 'trashed' => $i->trashed()]])->toArray();
        });

        return [
            'trip' => $trip,
            'flightInfo' => $trip->flight_info, 
            'shoppingList' => $groupedChecklist['藥妝'] ?? [], 
            'foodList' => $groupedChecklist['食物'] ?? [],
            'clothingList' => $groupedChecklist['衣物'] ?? [],
            'mustGoList' => $groupedChecklist['Must Go'] ?? [],
            'mustBuyList' => $groupedChecklist['Must Buy'] ?? [],
            'itinerary' => $trip->days,
        ];
    }
}
