<!-- resources/views/admin/logs/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Activity Logs')
@section('page_title', 'Activity Logs')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <p class="text-surface-400 text-sm">System activity and audit logs.</p>
        <span class="inline-flex items-center px-3 py-1 bg-surface-700 text-surface-300 text-sm rounded-lg">
            {{ $logs->total() }} entries
        </span>
    </div>

    {{-- Logs List --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-700/50">
                    <tr class="text-left text-xs font-semibold text-surface-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700">
                    @forelse($logs as $log)
                    @php
                        $actionColors = [
                            'created' => 'bg-emerald-500/10 text-emerald-400',
                            'updated' => 'bg-blue-500/10 text-blue-400',
                            'deleted' => 'bg-red-500/10 text-red-400',
                            'login' => 'bg-brand-500/10 text-brand-400',
                            'logout' => 'bg-surface-500/10 text-surface-400',
                            'sent' => 'bg-indigo-500/10 text-indigo-400',
                            'paid' => 'bg-emerald-500/10 text-emerald-400',
                        ];
                    @endphp
                    <tr class="hover:bg-surface-700/30">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $actionColors[$log->action] ?? 'bg-surface-500/10 text-surface-400' }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-surface-300 text-sm">
                            {{ $log->user->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm max-w-md truncate">
                            {{ $log->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-surface-500 text-xs font-mono">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm">
                            <span title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-surface-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p>No activity logs yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages())
    <div class="flex justify-center">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection