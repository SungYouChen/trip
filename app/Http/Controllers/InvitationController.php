<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripCollaborator;
use App\Models\User;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function revoke(User $user, Trip $trip, $invitationId)
    {
        // Only owner can revoke
        if (auth()->id() !== $trip->user_id) {
            abort(403);
        }

        $invitation = $trip->invitations()->findOrFail($invitationId);
        $invitation->delete();

        if (request()->ajax()) {
            return response()->json(['message' => '邀請已撤回或成員已移除。']);
        }

        return back()->with('success', '邀請已撤回或成員已移除。');
    }
}
