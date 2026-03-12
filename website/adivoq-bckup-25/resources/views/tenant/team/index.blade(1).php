@extends('layouts.tenant')

@section('title','Team')
@section('page_title','Team')

@section('content')

<div class="space-y-8">

    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-white mb-6">
            Team Members
        </h3>

        <form method="POST" action="{{ route('tenant.team.invite') }}" class="flex gap-4 mb-8">
            @csrf
            <input type="email" name="email" placeholder="Email"
                   class="bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-white text-sm" required>
            <select name="role" class="bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-white text-sm" required>
                <option value="manager">Manager</option>
                <option value="accountant">Accountant</option>
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
            </select>
            <input type="text" name="custom_message" placeholder="Add a welcome message (optional)" class="input-field">

            <button class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
                Invite
            </button>
        </form>
        
        

        <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-surface-700/50 text-surface-400">
                    <tr>
                        <th class="px-6 py-4 text-left">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700">
                    @foreach($team as $member)
                    <tr>
                        <td class="px-6 py-4 text-white">
                            {{ $member->user->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-surface-400">
                            {{ $member->user->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-surface-400">
                            {{ ucfirst($member->role) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="{{ $member->status=='active' ? 'text-green-400' : 'text-red-400' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                            <form method="POST" action="{{ route('tenant.team.resend', $member->id) }}">
    @csrf
    <button class="text-xs text-blue-400">Resend Invite</button>
</form>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            @if($member->status=='active')
                            <form method="POST" action="{{ route('tenant.team.suspend',$member->id) }}">
                                @csrf
                                <button class="text-xs text-amber-400">Suspend</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('tenant.team.activate',$member->id) }}">
                                @csrf
                                <button class="text-xs text-green-400">Activate</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('tenant.team.remove',$member->id) }}">
                                @csrf
                                <button class="text-xs text-red-400">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection