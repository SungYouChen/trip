<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userParam = $request->route('user');
        $trip = $request->route('trip');
        
        // Resolve target user (by username or object)
        $targetUser = ($userParam instanceof \App\Models\User) ? $userParam : \App\Models\User::where('username', $userParam)->first();
        $targetUserId = $targetUser ? $targetUser->id : null;

        // If it's a Trip object (Route Model Binding), get the id
        $tripId = ($trip instanceof \App\Models\Trip) ? $trip->id : $trip;

        // AUTH CHECK
        if (auth()->check()) {
            $currUser = auth()->user();
            
            // 1. If visiting own scope, always allow
            if ($targetUserId == $currUser->id) {
                return $next($request);
            }

            // 2. If visiting someone else's scope but with a specific trip
            if ($tripId) {
                $tripObj = ($trip instanceof \App\Models\Trip) ? $trip : \App\Models\Trip::find($tripId);
                
                if ($tripObj) {
                    // Allow if user is owner or collaborator
                    if ($tripObj->user_id == $currUser->id || $tripObj->collaborators()->where('user_id', $currUser->id)->exists()) {
                        return $next($request);
                    }
                }
            }
        }

        // If we reach here and it's not the user's space, block unless it's a public shared route
        if ($targetUserId && auth()->check() && auth()->id() != $targetUserId) {
             if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            return redirect()->route('home', ['user' => auth()->user()])->with('error', '您無權訪問該使用者的資料。');
        }

        return $next($request);
    }
}
