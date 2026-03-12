<?php
// app/Http/Middleware/IdentifyTenant.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // If system admin, skip tenant identification
        if ($user->is_system_admin) {
            return $next($request);
        }

        // Set tenant_id in session
        if ($user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);

            if (!$tenant || $tenant->status !== 'active') {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your account is suspended. Contact support.');
            }

            session(['tenant_id' => $tenant->id]);
            session(['tenant' => $tenant]);
        } else {
            auth()->logout();
            return redirect()->route('login')->with('error', 'No tenant assigned to your account.');
        }

        return $next($request);
    }
}