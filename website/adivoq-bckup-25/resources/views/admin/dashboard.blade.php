<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-surface-400 text-xs uppercase tracking-wider">Total Tenants</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['tenants'] }}</p>
                </div>
                <div class="w-10 h-10 bg-brand-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-surface-400 text-xs uppercase tracking-wider">Active</p>
                    <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $stats['active_tenants'] }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-surface-400 text-xs uppercase tracking-wider">Users</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['users'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-surface-400 text-xs uppercase tracking-wider">Waitlist</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['waitlist'] }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-surface-400 text-xs uppercase tracking-wider">Messages</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['messages'] }}</p>
                </div>
                <div class="w-10 h-10 bg-rose-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-surface-400 text-xs uppercase tracking-wider">Blog Posts</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['blog_posts'] }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Recent Tenants --}}
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Recent Tenants</h3>
                <a href="{{ route('admin.tenants.index') }}" class="text-sm text-brand-400 hover:text-brand-300">View all →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentTenants as $tenant)
                <div class="flex items-center justify-between py-2 border-b border-surface-700 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-brand-600/20 rounded-lg flex items-center justify-center">
                            <span class="text-brand-400 text-xs font-semibold">{{ strtoupper(substr($tenant->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-medium">{{ $tenant->name }}</p>
                            <p class="text-surface-500 text-xs">{{ $tenant->owner->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $tenant->status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-surface-500/10 text-surface-400' }}">
                        {{ ucfirst($tenant->status) }}
                    </span>
                </div>
                @empty
                <p class="text-surface-500 text-sm text-center py-4">No tenants yet</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Messages --}}
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Recent Messages</h3>
                <a href="{{ route('admin.messages.index') }}" class="text-sm text-brand-400 hover:text-brand-300">View all →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentMessages as $message)
                <a href="{{ route('admin.messages.show', $message) }}" class="block py-2 border-b border-surface-700 last:border-0 hover:bg-surface-700/30 -mx-2 px-2 rounded">
                    <div class="flex items-center justify-between">
                        <p class="text-white text-sm font-medium">{{ $message->name }}</p>
                        <span class="text-surface-500 text-xs">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-surface-400 text-xs mt-0.5 line-clamp-1">{{ $message->message }}</p>
                </a>
                @empty
                <p class="text-surface-500 text-sm text-center py-4">No messages yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.tenants.create') }}" class="flex items-center gap-3 p-4 bg-surface-700/50 rounded-lg hover:bg-surface-700 transition-colors">
                <div class="w-10 h-10 bg-brand-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-sm text-surface-300">Add Tenant</span>
            </a>
            <a href="{{ route('admin.blog.create') }}" class="flex items-center gap-3 p-4 bg-surface-700/50 rounded-lg hover:bg-surface-700 transition-colors">
                <div class="w-10 h-10 bg-emerald-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <span class="text-sm text-surface-300">New Blog Post</span>
            </a>
            <a href="{{ route('admin.roadmap.create') }}" class="flex items-center gap-3 p-4 bg-surface-700/50 rounded-lg hover:bg-surface-700 transition-colors">
                <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <span class="text-sm text-surface-300">Add Roadmap</span>
            </a>
            <a href="{{ route('admin.waitlist.index') }}" class="flex items-center gap-3 p-4 bg-surface-700/50 rounded-lg hover:bg-surface-700 transition-colors">
                <div class="w-10 h-10 bg-amber-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-sm text-surface-300">Export Waitlist</span>
            </a>
        </div>
    </div>
</div>
@endsection