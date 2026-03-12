<?php
// app/Http/Middleware/AdminAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->is_system_admin) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}