<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    
    public function index()
    {
        $team = TeamMember::with('user')
            ->where('tenant_id', session('tenant_id'))
            ->get();

        return view('tenant.team.index', compact('team'));
    }

public function invite(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'role' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        $user = User::create([
            'name' => $request->email,
            'email' => $request->email,
            'password' => Hash::make(\Str::random(12)),
            'role' => $request->role,
        ]);
    }

    $token = \Str::random(40);

    // Always update the token and save
    $teamMember = \App\Models\TeamMember::updateOrCreate([
        'tenant_id' => session('tenant_id'),
        'user_id' => $user->id,
    ], [
        'role' => $request->role,
        'invited_by' => auth()->id(),
        'invited_at' => now(),
        'status' => 'invited',
        'invite_token' => $token,
        'invite_expires_at' => now()->addDays(7),
        'custom_message' => $request->custom_message,
    ]);

    // Force save in case updateOrCreate doesn't update token
    $teamMember->invite_token = $token;
    $teamMember->invite_expires_at = now()->addDays(7);
    $teamMember->save();

    // Reload from DB to be 100% sure
    $teamMember = $teamMember->fresh();

    // Now send the email with the correct token
    \Mail::to($user->email)->send(new \App\Mail\TeamInviteMail($teamMember));

    return back()->with('success', 'Team member invited.');
}
    public function remove($id)
    {
        TeamMember::where('id', $id)
            ->where('tenant_id', session('tenant_id'))
            ->delete();

        return back()->with('success', 'Team member removed.');
    }

    public function suspend($id)
    {
        $member = TeamMember::where('id', $id)
            ->where('tenant_id', session('tenant_id'))
            ->firstOrFail();

        $member->status = 'suspended';
        $member->save();

        return back()->with('success', 'Team member suspended.');
    }

    public function activate($id)
    {
        $member = TeamMember::where('id', $id)
            ->where('tenant_id', session('tenant_id'))
            ->firstOrFail();

        $member->status = 'active';
        $member->save();

        return back()->with('success', 'Team member activated.');
    }
    
public function resend($id)
{
    $member = TeamMember::with('user')->findOrFail($id);

    $token = Str::random(40);
    $member->invite_token = $token;
    $member->invited_at = now();
    $member->status = 'invited';
    $member->invite_expires_at = now()->addDays(7);
    $member->save();

    \Mail::to($member->user->email)->send(new \App\Mail\TeamInviteMail($member));

    return back()->with('success', 'Invitation resent.');
}


public function updateRole(Request $request, $id)
{
    $member = \App\Models\TeamMember::findOrFail($id);
    $member->role = $request->role;
    $member->permissions = getRolePermissions($request->role); // auto-assign permissions
    $member->save();

    return back()->with('success', 'Role updated.');
}

public function profile($id)
{
    $member = \App\Models\TeamMember::with('user')->findOrFail($id);

    // Revenue splits for this member
    $splits = \App\Models\RevenueSplit::where('team_member_id', $member->id)->get();

    // Recent activity
    $activity = \App\Models\ActivityLog::where('user_id', $member->user_id)->latest()->take(20)->get();

    return view('tenant.team.profile', compact('member', 'splits', 'activity'));
}

public function analytics()
{
    $members = \App\Models\TeamMember::with('user')->get();

    // Revenue per member
    $revenue = [];
    foreach ($members as $member) {
        $revenue[$member->user->name] = \App\Models\RevenueSplit::where('team_member_id', $member->id)->sum('amount');
    }

    // Activity count per member
    $activity = [];
    foreach ($members as $member) {
        $activity[$member->user->name] = \App\Models\ActivityLog::where('user_id', $member->user_id)->count();
    }

    return view('tenant.team.analytics', compact('members', 'revenue', 'activity'));
}

}