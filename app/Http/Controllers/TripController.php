<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\User;
use App\Models\TripCollaborator;
use App\Models\TripComment;
use App\Mail\TripInvitationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TripController extends Controller
{
    public function index(User $user)
    {
        $trips = Trip::withTrashed()
            ->with(['user', 'collaborators'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('collaborators', fn ($cq) => $cq->where('user_id', $user->id));
            })
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

        $item = $trip->checklistItems()->create($validated);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Item added successfully!',
                'item' => $item
            ]);
        }

        return back()->with('success', 'Item added!');
    }

    public function toggleItem(User $user, Trip $trip, $id, Request $request)
    {
        $item = $trip->checklistItems()->findOrFail($id);
        $item->is_completed = !$item->is_completed;
        $item->save();

        if ($request->ajax()) {
            return response()->json([
                'message' => $item->is_completed ? '已完成！' : '已取消完成。',
                'is_completed' => $item->is_completed
            ]);
        }

        return back();
    }

    public function deleteItem(User $user, Trip $trip, $id)
    {
        $trip->checklistItems()->findOrFail($id)->delete(); // soft delete
        if (request()->ajax()) return response()->json(['message' => '清單項目已封存。']);
        return back()->with('success', '清單項目已封存。');
    }

    public function restoreItem(User $user, Trip $trip, $id)
    {
        $item = $trip->checklistItems()->withTrashed()->findOrFail($id);
        $item->restore();
        if (request()->ajax()) return response()->json(['message' => '清單項目已還原！']);
        return back()->with('success', '清單項目已還原！');
    }

    public function forceDeleteItem(User $user, Trip $trip, $id)
    {
        $item = $trip->checklistItems()->withTrashed()->findOrFail($id);
        $item->forceDelete();
        if (request()->ajax()) return response()->json(['message' => '清單項目已永久刪除。']);
        return back()->with('success', '清單項目已永久刪除。');
    }

    public function store(User $user, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date|required_without:estimated_days',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_days' => 'nullable|integer|min:1|required_without:start_date',
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
        if ($trip->start_date && $trip->end_date) {
            $start = \Carbon\Carbon::parse($trip->start_date);
            $end = \Carbon\Carbon::parse($trip->end_date);
            $dayNum = 1;

            while ($start <= $end) {
                $trip->days()->create([
                    'date' => $start->toDateString(),
                    'day_number' => $dayNum++,
                    'summary' => '今日尚未安排摘要...',
                    'accommodation' => null,
                    'accommodation_details' => []
                ]);
                $start->addDay();
            }
        } else {
            $daysCount = $trip->estimated_days ?? 1;
            for ($i = 1; $i <= $daysCount; $i++) {
                $trip->days()->create([
                    'date' => null,
                    'day_number' => $i,
                    'summary' => '今日尚未安排摘要...',
                    'accommodation' => null,
                    'accommodation_details' => []
                ]);
            }
        }

        if (request()->ajax()) {
            return response()->json([
                'message' => '旅程已建立，已為您預建行程表！',
                'redirect' => route('trip.show', ['user' => $user, 'trip' => $trip])
            ]);
        }
        return redirect()->route('trip.show', ['user' => $user, 'trip' => $trip])->with('success', '旅程已建立，已為您預建行程表！');
    }
    public function addDay(User $user, Trip $trip)
    {
        $lastDayNum = $trip->days()->max('day_number') ?? 0;
        $nextDayNum = $lastDayNum + 1;
        $newDate = null;

        if ($trip->start_date) {
            $newDate = \Carbon\Carbon::parse($trip->start_date)->addDays($nextDayNum - 1);
            $trip->end_date = $newDate->toDateString();
            $trip->save();
        } else {
            $trip->estimated_days = $nextDayNum;
            $trip->save();
        }

        $trip->days()->create([
            'date' => $newDate ? $newDate->toDateString() : null,
            'day_number' => $nextDayNum,
            'summary' => '今日尚未安排摘要...',
            'accommodation' => null,
            'accommodation_details' => []
        ]);

        if (request()->ajax()) return response()->json(['message' => '已新增一天的行程卡片！']);
        return back()->with('success', '已新增一天的行程卡片！');
    }

    public function destroy(User $user, Trip $trip)
    {
        if ($trip->user_id !== auth()->id()) {
            if (request()->ajax()) return response()->json(['message' => '只有旅程建立者可以封存此旅程。'], 403);
            return back()->with('error', '只有旅程建立者可以封存此旅程。');
        }
        $trip->delete(); // soft delete
        if (request()->ajax()) return response()->json(['message' => '旅程已封存。', 'redirect' => route('home', ['user' => auth()->user()])]);
        return redirect()->route('home', ['user' => auth()->user()])->with('success', '旅程已封存。可在「查看封存」中還原。');
    }

    public function restore(User $user, $tripId)
    {
        $trip = Trip::withTrashed()
            ->where(fn ($q) => $q->where('id', $tripId))
            ->firstOrFail();
        $trip->restore();
        if (request()->ajax()) return response()->json(['message' => '旅程已還原！']);
        return redirect()->route('home', ['user' => $user])->with('success', '旅程已還原！');
    }

    public function forceDelete(User $user, $tripId)
    {
        $trip = Trip::withTrashed()->where(fn ($q) => $q->where('id', $tripId)->where('user_id', auth()->id()))->firstOrFail();
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
        if (request()->ajax()) return response()->json(['message' => '旅程及所有子項目已永久刪除。', 'redirect' => route('home', ['user' => $user])]);
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
            'outbound_time_start' => 'nullable|string|max:100',
            'outbound_time_end' => 'nullable|string|max:100',
            'outbound_route' => 'nullable|string|max:100',
            'inbound_date' => 'nullable|string|max:50',
            'inbound_time_start' => 'nullable|string|max:100',
            'inbound_time_end' => 'nullable|string|max:100',
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
                'time_start' => $validated['outbound_time_start'] ?? '',
                'time_end' => $validated['outbound_time_end'] ?? '',
                'route' => $validated['outbound_route'] ?? ''
            ],
            'inbound' => [
                'date' => $validated['inbound_date'] ?? '',
                'time_start' => $validated['inbound_time_start'] ?? '',
                'time_end' => $validated['inbound_time_end'] ?? '',
                'route' => $validated['inbound_route'] ?? ''
            ]
        ];

        $trip->update(['flight_info' => $flightInfo]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Transport information updated!']);
        }

        return back()->with('success', '交通資訊已更新！');
    }

    public function update(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date|required_without:estimated_days',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_days' => 'nullable|integer|min:1|required_without:start_date',
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

        $oldStartDate = $trip->start_date ? clone $trip->start_date : null;

        $trip->update([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'estimated_days' => $validated['estimated_days'] ?? null,
            'base_currency' => $validated['base_currency'],
            'target_currency' => $validated['target_currency'],
            'exchange_rate' => $validated['exchange_rate'],
            'cover_image' => $trip->cover_image,
            'flight_info' => $flightInfo,
        ]);

        // Auto-update itinerary days dates if the start_date changed
        if ($trip->start_date && (!$oldStartDate || $trip->start_date->notEqualTo($oldStartDate))) {
            $days = $trip->days()->orderBy('day_number')->get();
            $start = \Carbon\Carbon::parse($trip->start_date);
            foreach ($days as $day) {
                // In case older data doesn't have day_number, skip or set. 
                // We've backfilled it so it should be fine.
                $dNum = $day->day_number ?: 1;
                $day->update([
                    'date' => (clone $start)->addDays($dNum - 1)->toDateString()
                ]);
            }
            if ($days->count() > 0) {
                $trip->end_date = (clone $start)->addDays($days->count() - 1)->toDateString();
                $trip->save();
            }
        }

        if ($request->ajax()) {
            return response()->json(['message' => '旅程設定與封面已更新！']);
        }

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
        if (request()->ajax()) return response()->json(['message' => $msg]);
        return back()->with('success', $msg);
    }

    public function indexShared($token)
    {
        /** @var Trip $trip */
        $trip = Trip::where(fn ($q) => $q->where('share_token', $token)->where('is_public', true))->firstOrFail();

        $data = $this->getTripShowData($trip);
        $data['isShared'] = true;
        $data['user'] = $trip->user; // Pass owner
        return view('trips.show', $data);
    }

    /**
     * @param Trip $trip
     */
    public function addCollaborator(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $email = $validated['email'];

        // 1. Check if owner
        if ($email == $trip->user->email) {
            return back()->with('error', '您已經是此行程的擁有者了。');
        }

        // 2. Check if already a collaborator
        $existing = TripCollaborator::where('trip_id', $trip->id)
            ->where('email', $email)
            ->first();

        if ($existing) {
            return back()->with('error', '此 Email 已經被邀請過或已是協作者。');
        }

        // 3. Create invitation
        $token = Str::random(64);
        $targetUser = User::where('email', $email)->first();

        $collaborator = TripCollaborator::create([
            'trip_id' => $trip->id,
            'user_id' => $targetUser ? $targetUser->id : null,
            'email' => $email,
            'role' => 'editor',
            'status' => 'pending',
            'token' => $token,
            'invited_by' => auth()->id()
        ]);

        // 4. Send email
        try {
            Mail::to($email)->send(new TripInvitationMail($trip, auth()->user(), $token));
        } catch (\Exception $e) {
            \Log::error('Mail failed: ' . $e->getMessage());
            // We still keep the invitation in DB, but notify the user
            return back()->with('info', '邀請已建立，但郵件發送失敗（請確認您的 SMTP 設定）。');
        }

        if ($request->ajax()) {
            return response()->json(['message' => '邀請函已發送！']);
        }

        return back()->with('success', '邀請函已發送至：' . $email);
    }

    public function acceptInvitation($token)
    {
        $invitation = TripCollaborator::where('token', $token)->firstOrFail();

        if ($invitation->status === 'accepted') {
            return redirect()->route('trip.show', ['user' => $invitation->trip->user, 'trip' => $invitation->trip])
                ->with('info', '您早已加入此旅程囉！');
        }

        // If not logged in, redirect to register with token
        if (!auth()->check()) {
            session(['invitation_token' => $token]);
            return redirect()->route('story')->with('info', '歡迎參與！請先登入或註冊帳件，系統將為您自動加入旅程。');
        }

        // Check if current user is the one invited (or if email matches)
        $user = auth()->user();
        if ($user->email !== $invitation->email) {
            // Optional: Support multi-account switching, but for now simple check
            return redirect()->route('home', ['user' => $user])->with('error', '此連結僅限被邀請的 Email 使用。');
        }

        // Accept
        $invitation->update([
            'user_id' => $user->id,
            'status' => 'accepted'
        ]);

        return redirect()->route('trip.show', ['user' => $invitation->trip->user, 'trip' => $invitation->trip])
            ->with('success', '歡迎加入！您現在可以共同編輯這份旅程了。');
    }

    public function updateProfile(User $user, Request $request)
    {
        // 1. Handle Restore Default (Explicit '1' check)
        if ($request->input('restore_default') == '1') {
            if ($user->background_image && str_contains($user->background_image, 'backgrounds/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->background_image));
            }
            if ($user->avatar && str_contains($user->avatar, 'avatars/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }
            $user->update([
                'avatar' => null,
                'background_image' => null,
                'bg_opacity' => 40,    // Muji Default
                'bg_blur' => 5,        // Muji Default
                'bg_style' => 'center',
                'bg_width' => 45,      // Muji Default
            ]);

            if ($request->ajax()) {
                return response()->json(['message' => '已恢復官方預設設定！']);
            }
            return back()->with('success', '已恢復預設背景與設定！');
        }

        // 2. Handle Settings & Upload (iPhone Optimized)
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'avatar' => 'nullable|max:30720',
            'background_image' => 'nullable|max:30720',
            'bg_opacity' => 'nullable|integer|min:0|max:100',
            'bg_blur' => 'nullable|integer|min:0|max:20',
            'bg_style' => 'nullable|string|in:full,center',
            'bg_width' => 'nullable|integer|min:0|max:100',
            'current_password' => 'nullable|string|required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $updateData = [];
        if ($request->filled('name')) $updateData['name'] = $validated['name'];
        if (isset($validated['bg_opacity'])) $updateData['bg_opacity'] = $validated['bg_opacity'];
        if (isset($validated['bg_blur'])) $updateData['bg_blur'] = $validated['bg_blur'];
        if (isset($validated['bg_style'])) $updateData['bg_style'] = $validated['bg_style'];
        if ($request->has('bg_width')) $updateData['bg_width'] = $request->bg_width;

        // Handle Avatar Removal
        if ($request->has('remove_avatar')) {
            if ($user->avatar && str_contains($user->avatar, 'avatars/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }
            $updateData['avatar'] = null;
        }

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && str_contains($user->avatar, 'avatars/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar'] = Storage::url($path);
        }

        // Handle Background Upload
        if ($request->hasFile('background_image')) {
            if ($user->background_image && str_contains($user->background_image, 'backgrounds/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->background_image));
            }
            $path = $request->file('background_image')->store('backgrounds', 'public');
            $updateData['background_image'] = Storage::url($path);
        }

        // Handle Password Change
        if ($request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($validated['current_password'], $user->password)) {
                return back()->with('error', '目前的密碼不正確。');
            }
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validated['new_password']);
        }

        if (!empty($updateData)) {
            $user->update($updateData);

            if ($request->ajax()) {
                return response()->json(['message' => '個人設定已更新！']);
            }

            return back()->with('success', '個人帳號與設定已更新！');
        }

        if ($request->ajax()) {
            return response()->json(['message' => '未偵測到任何變更。']);
        }
        return back()->with('info', '未變更任何設定。');
    }

    public function removeCollaborator(User $user, Trip $trip, $collaboratorId, Request $request)
    {
        $trip->collaborators()->detach($collaboratorId);
        if ($request->ajax()) {
            return response()->json(['message' => '協作者已移除。']);
        }

        return back()->with('success', '已移除協作者。');
    }

    public function markCollaborationAsNotified(User $user, Trip $trip)
    {
        if (auth()->id() !== $user->id) abort(403);

        $user->collaboratingTrips()->updateExistingPivot($trip->id, ['is_notified' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * @param Trip $trip
     */
    private function getTripShowData($trip)
    {
        $trip->load(['days' => function ($q) {
            $q->withTrashed()->orderBy('date'); }, 'collaborators', 'tripComments']);
        $checklistItems = $trip->checklistItems()->withTrashed()->get();
        $groupedChecklist = $checklistItems->groupBy('category')->map(function ($items) {
            return $items->mapWithKeys(fn ($i) => [$i->id => ['name' => $i->name, 'trashed' => $i->trashed()]])->toArray();
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
            'globalComments' => $trip->tripComments->map(function($comment) use ($trip) {
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
                    'time' => $comment->created_at->diffForHumans(),
                    'can_delete' => $canDelete
                ];
            }),
        ];
    }

    public function storeTripComment(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        $trip->tripComments()->create([
            'user_id' => auth()->id(),
            'user_name' => $validated['user_name'] ?? (auth()->check() ? auth()->user()->name : '匿名旅伴'),
            'content' => $validated['content'],
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => '發言成功！']);
        }

        return back()->with('success', '發言成功！');
    }

    public function storeTripCommentShared($token, Request $request)
    {
        $trip = Trip::where('share_token', $token)->where('is_public', true)->firstOrFail();
        
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $trip->tripComments()->create([
            'user_id' => null,
            'user_name' => $validated['user_name'],
            'content' => $validated['content'],
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => '發言成功！']);
        }

        return back()->with('success', '發言成功！');
    }

    public function deleteTripComment($commentId)
    {
        $comment = TripComment::findOrFail($commentId);
        $trip = $comment->trip;

        // AUTH: Trip owner OR Comment author
        if (auth()->check() && (auth()->id() === $trip->user_id || auth()->id() === $comment->user_id)) {
            $comment->delete();
            return back()->with('success', '留言已刪除。');
        }

        return abort(403, '權限不足。');
    }

    public function assignSpotToDay(User $user, Trip $trip, $id, Request $request)
    {
        $item = $trip->checklistItems()->findOrFail($id);
        $date = $request->input('date');

        $day = $trip->days()->whereDate('date', $date)->firstOrFail();

        $day->events()->create([
            'time' => '全天/彈性',
            'activity' => $item->name,
            'note' => '從景點收納箱指派',
            'sort_order' => $day->events()->count(),
        ]);

        return response()->json([
            'message' => "「{$item->name}」已成功指派至 {$date}！",
            'success' => true
        ]);
    }

    public function fetchExchangeRate(User $user, Request $request)
    {
        $base = strtoupper($request->query('base', 'TWD'));
        $target = strtoupper($request->query('target', 'JPY'));

        try {
            // 使用支援廣泛幣別 (含 TWD) 的 API
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get("https://open.er-api.com/v6/latest/{$base}");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['result'] === 'success' && isset($data['rates'][$target])) {
                    $rate = (float)$data['rates'][$target];
                    return response()->json(['rate' => $rate]);
                }
            }

            return response()->json(['error' => '目前暫時無法取得該幣別匯率（來源限制）'], 404);
        } catch (\Exception $e) {
            \Log::error('Exchange error: ' . $e->getMessage());
            return response()->json(['error' => 'API 連線失敗，請檢查網路狀態。'], 500);
        }
    }

    public function convertCommentToItem(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'type' => 'required|in:shopping,spot',
            'category' => 'nullable|string',
        ]);

        $category = $validated['category'] ?? ($validated['type'] === 'shopping' ? 'Must Buy' : 'Must Go');
        
        $item = $trip->checklistItems()->create([
            'type' => $validated['type'],
            'category' => $category,
            'name' => $validated['content'],
        ]);

        return response()->json([
            'message' => '已成功將留言轉入清單！',
            'item' => $item
        ]);
    }

    public function reorderChecklist(User $user, Trip $trip, Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:checklist_items,id',
            'category' => 'required|string',
            'order' => 'required|array',
        ]);

        // Update category if changed
        $item = $trip->checklistItems()->findOrFail($validated['item_id']);
        $item->update(['category' => $validated['category']]);

        // Update sort orders for all items in the new group if provided
        foreach ($validated['order'] as $index => $id) {
            $trip->checklistItems()->where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['message' => '清單順序及分類已同步']);
    }
}
