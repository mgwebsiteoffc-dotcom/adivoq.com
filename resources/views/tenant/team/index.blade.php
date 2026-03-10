@extends('layouts.tenant')
@section('title','Team')
@section('page_title','Team Management')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Invite --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Invite Team Member</h3>
        <form method="POST" action="{{ route('dashboard.team.invite') }}" class="space-y-3">
            @csrf
            <input type="email" name="email" required placeholder="email@example.com"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm">

            <select name="role" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                <option value="viewer">Viewer</option>
                <option value="editor">Editor</option>
                <option value="accountant">Accountant</option>
                <option value="manager">Manager</option>
            </select>

            <button class="w-full px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">
                Invite
            </button>
        </form>

        <p class="text-xs text-gray-500 mt-3">
            Invites expire in 7 days. The link will be shown after creating invite.
        </p>
    </div>

    {{-- Invitations --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Pending Invitations</h3>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse($invitations as $inv)
                <div class="border rounded-lg p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $inv->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Role: <span class="font-semibold capitalize">{{ $inv->role }}</span>
                                • Expires: {{ $inv->expires_at->diffForHumans() }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('dashboard.team.cancel-invitation', $inv) }}" onsubmit="return confirm('Cancel invite?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-black text-red-600 hover:underline">Cancel</button>
                        </form>
                    </div>

                    <div class="mt-2 text-xs text-gray-600 font-mono break-all bg-gray-50 border rounded-lg p-2">
                        {{ route('invitation.show', $inv->token) }}
                    </div>

                    <button type="button"
                            onclick="navigator.clipboard.writeText('{{ route('invitation.show', $inv->token) }}'); this.innerText='Copied'; setTimeout(()=>this.innerText='Copy Link',1500)"
                            class="mt-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-black hover:bg-gray-200">
                        Copy Link
                    </button>
                </div>
            @empty
                <p class="text-sm text-gray-500">No pending invitations.</p>
            @endforelse
        </div>
    </div>

    {{-- ✅ Team Activity Panel --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Recent Team Activity</h3>

        @php
            $icons = [
                'team_invited' => ['icon' => 'fa-paper-plane', 'color' => 'text-indigo-600 bg-indigo-100'],
                'team_invite_cancelled' => ['icon' => 'fa-ban', 'color' => 'text-red-600 bg-red-100'],
                'team_role_updated' => ['icon' => 'fa-user-gear', 'color' => 'text-blue-600 bg-blue-100'],
                'team_suspended' => ['icon' => 'fa-user-slash', 'color' => 'text-orange-600 bg-orange-100'],
                'team_reactivated' => ['icon' => 'fa-user-check', 'color' => 'text-green-600 bg-green-100'],
                'team_removed' => ['icon' => 'fa-user-xmark', 'color' => 'text-red-600 bg-red-100'],
            ];
        @endphp

        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse($teamActivity as $log)
                @php
                    $cfg = $icons[$log->action] ?? ['icon' => 'fa-circle', 'color' => 'text-gray-500 bg-gray-100'];
                @endphp

                <div class="flex items-start gap-3 border-b pb-3 last:border-0 last:pb-0">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ $cfg['color'] }} flex-shrink-0">
                        <i class="fas {{ $cfg['icon'] }} text-sm"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-black text-gray-800">
                            {{ strtoupper(str_replace('team_', '', $log->action)) }}
                        </p>
                        <p class="text-sm text-gray-700">
                            {{ $log->description ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $log->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No team activity yet.</p>
            @endforelse
        </div>

        <p class="text-xs text-gray-400 mt-3">
            Tip: Team actions are recorded for auditing and accountability.
        </p>
    </div>
</div>

{{-- Team Members --}}
<div class="bg-white rounded-xl border p-6">
    <h3 class="text-sm font-black text-gray-900 mb-4">Team Members</h3>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">User</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Role</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($members as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-black text-gray-900">{{ $u->name }}</p>
                            <p class="text-xs text-gray-500">{{ $u->email }}</p>
                        </td>

                        <td class="px-4 py-3">
                            @if($u->role === 'owner')
                                <span class="text-xs font-black bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Owner</span>
                            @else
                                <form method="POST" action="{{ route('dashboard.team.update-role', $u) }}">
                                    @csrf @method('PUT')
                                    <select name="role" onchange="this.form.submit()" class="px-2 py-1 border rounded-lg text-xs">
                                        @foreach(['manager','accountant','editor','viewer'] as $r)
                                            <option value="{{ $r }}" @selected($u->role===$r)>{{ ucfirst($r) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <span class="text-xs font-black px-2 py-0.5 rounded-full {{ $u->status==='active'?'bg-green-100 text-green-700':'bg-red-100 text-red-700' }}">
                                {{ ucfirst($u->status) }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">
                            @if($u->role !== 'owner')
                                @if($u->status === 'active')
                                    <form method="POST" action="{{ route('dashboard.team.suspend',$u) }}" class="inline" onsubmit="return confirm('Suspend user?')">
                                        @csrf
                                        <button class="text-xs font-black text-orange-600 hover:underline mr-2">Suspend</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dashboard.team.reactivate',$u) }}" class="inline">
                                        @csrf
                                        <button class="text-xs font-black text-green-600 hover:underline mr-2">Reactivate</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('dashboard.team.remove',$u) }}" class="inline" onsubmit="return confirm('Remove user?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-black text-red-600 hover:underline">Remove</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection