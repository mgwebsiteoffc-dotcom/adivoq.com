<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeamRoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // allow owner always if included OR if no roles passed
        if (empty($roles)) {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            // for AJAX requests, return JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            return response()->view('errors.403', [
                'message' => 'You do not have permission to access this page.',
                'required_roles' => $roles,
                'current_role' => $user->role,
            ], 403);
        }

        return $next($request);
    }
}