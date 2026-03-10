<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    public function stop(Request $request)
    {
        $adminId = session('admin_impersonating');

        if (!$adminId) {
            return redirect()->route('admin.dashboard');
        }

        // logout tenant user (web guard)
        auth()->logout();

        // login back admin
        auth()->guard('admin')->loginUsingId($adminId);

        // remove flag + regenerate
        session()->forget('admin_impersonating');
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Impersonation ended. Back to Admin.');
    }
}