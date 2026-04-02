<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedbackController extends Controller
{
    public function index(User $user)
    {
        // Safety check: only allow viewing own feedback or if is admin
        if (auth()->id() !== $user->id && !auth()->user()->is_admin) {
            abort(403);
        }

        $isAdmin = auth()->user()->is_admin;

        if ($isAdmin) {
            // Admin sees all top-level feedbacks
            $feedbacks = Feedback::whereNull('parent_id')
                ->with(['user', 'replies.user'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // User only sees their own top-level feedbacks
            $feedbacks = Feedback::where('user_id', $user->id)
                ->whereNull('parent_id')
                ->with(['replies.user'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('feedback.index', compact('feedbacks', 'user', 'isAdmin'));
    }

    public function store(User $user, Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'parent_id' => 'nullable|exists:feedbacks,id'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                $imagePath = $file->store('feedbacks', 'public');
            }
        }

        $feedback = Feedback::create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_admin_reply' => auth()->user()->is_admin && $validated['parent_id'] ? true : false,
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => '訊息已送出！']);
        }

        return back()->with('success', '訊息已送出！');
    }

    public function destroy(User $user, Feedback $feedback)
    {
        if (auth()->id() !== $feedback->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        if ($feedback->image_path) {
            Storage::disk('public')->delete($feedback->image_path);
        }

        $feedback->delete();
        if (request()->ajax()) {
            return response()->json(['message' => '訊息已刪除。']);
        }
        return back()->with('success', '訊息已刪除。');
    }
}
