<!-- resources/views/admin/users/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Admin Users')
@section('page_title', 'Admin Users')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <p class="text-surface-400 text-sm">Users with admin access to the platform.</p>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-700/50">
                    <tr class="text-left text-xs font-semibold text-surface-400 uppercase tracking-wider">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Last Login</th>
                        <th class="px-6 py-4">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-surface-700/30">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-brand-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $user->name }}</p>
                                    <p class="text-surface-500 text-sm">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-500/10 text-brand-400">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm">
                            {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-surface-500">No admin users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="flex justify-center">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection