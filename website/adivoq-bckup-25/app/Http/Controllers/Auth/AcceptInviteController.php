<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AcceptInviteController extends Controller
{
public function showForm($token)
{
    $teamMember = \App\Models\TeamMember::where('invite_token', $token)->firstOrFail();

    if ($teamMember->invite_expires_at && now()->greaterThan($teamMember->invite_expires_at)) {
        abort(403, 'This invite link has expired.');
    }

    $user = $teamMember->user;
    return view('auth.accept-invite', compact('user', 'token'));
}

public function accept(Request $request, $token)
{
    $teamMember = \App\Models\TeamMember::where('invite_token', $token)->firstOrFail();
    $user = $teamMember->user;

    $request->validate([
        'name' => 'required|string|max:255',
        'password' => 'required|confirmed|min:8',
    ]);

    $user->name = $request->name;
    $user->password = \Hash::make($request->password);
    $user->tenant_id = $teamMember->tenant_id; // ✅ FIX: assign tenant
    $user->save();

    $teamMember->status = 'active';
    $teamMember->accepted_at = now();
    $teamMember->invite_token = null;
    $teamMember->save();

    auth()->login($user);

    return redirect('/dashboard')->with('success', 'Welcome! Your account is now active.');
}
}