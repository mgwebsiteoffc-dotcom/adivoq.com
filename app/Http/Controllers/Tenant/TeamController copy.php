<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ActivityLog;

class TeamController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;

        $members = User::where('tenant_id', $tenant->id)->orderByRaw("role='owner' DESC")->orderBy('name')->get();
        $invitations = TeamInvitation::where('tenant_id', $tenant->id)->latest()->get();

        return view('tenant.team.index', compact('members','invitations'));
    }

    public function invite(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:manager,accountant,editor,viewer',
        ]);

        // prevent inviting existing user
        if (User::where('tenant_id', $tenant->id)->where('email', $request->email)->exists()) {
            return back()->with('error', 'This email already exists in your team.');
        }

        $inv = TeamInvitation::create([
            'tenant_id' => $tenant->id,
            'email' => $request->email,
            'role' => $request->role,
            'token' => Str::random(64),
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        $link = route('invitation.show', $inv->token);
        ActivityLog::record(auth()->user(), 'team_invited', 'Invited team member: ' . $inv->email, [
    'email' => $inv->email,
    'role' => $inv->role,
    'token' => $inv->token,
]);

        return back()->with('success', 'Invitation created. Share this link: ' . $link);
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) abort(403);
        if ($user->role === 'owner') return back()->with('error', 'Owner role cannot be changed.');

        $request->validate([
            'role' => 'required|in:manager,accountant,editor,viewer',
        ]);

        $user->update(['role' => $request->role]);
ActivityLog::record(auth()->user(), 'team_role_updated', 'Updated role for: ' . $user->email, [
    'user_id' => $user->id,
    'email' => $user->email,
    'new_role' => $request->role,
]);
        return back()->with('success', 'Role updated.');
    }

    public function suspend(User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) abort(403);
        if ($user->role === 'owner') return back()->with('error', 'Owner cannot be suspended.');

        $user->update(['status' => 'suspended']);
        ActivityLog::record(auth()->user(), 'team_suspended', 'Suspended user: ' . $user->email, [
    'user_id' => $user->id,
    'email' => $user->email,
]);
        return back()->with('success', 'User suspended.');
    }

    public function reactivate(User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) abort(403);
        $user->update(['status' => 'active']);
        ActivityLog::record(auth()->user(), 'team_reactivated', 'Reactivated user: ' . $user->email, [
    'user_id' => $user->id,
    'email' => $user->email,
]);
        return back()->with('success', 'User reactivated.');
    }

    public function remove(User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) abort(403);
        if ($user->role === 'owner') return back()->with('error', 'Owner cannot be removed.');
ActivityLog::record(auth()->user(), 'team_removed', 'Removed user: ' . $user->email, [
    'user_id' => $user->id,
    'email' => $user->email,
]);
        $user->delete();
        return back()->with('success', 'User removed.');
    }

    public function cancelInvitation(TeamInvitation $invitation)
    {
        if ($invitation->tenant_id !== auth()->user()->tenant_id) abort(403);
ActivityLog::record(auth()->user(), 'team_invite_cancelled', 'Cancelled invitation: ' . $invitation->email, [
    'invitation_id' => $invitation->id,
    'email' => $invitation->email,
]);
        $invitation->delete();
        return back()->with('success', 'Invitation cancelled.');
    }
}