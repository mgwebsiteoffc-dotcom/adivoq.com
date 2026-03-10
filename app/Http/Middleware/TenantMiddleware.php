<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->tenant || $user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is not active.');
        }

        if ($user->tenant->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended.');
        }

        // Share tenant with all views
        view()->share('currentTenant', $user->tenant);
        view()->share('currentUser', $user);

        return $next($request);
    }
}