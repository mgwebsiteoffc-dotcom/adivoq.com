<!-- resources/views/admin/tenants/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Tenants')
@section('page_title', 'Tenants')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-surface-400 text-sm">Manage all creator accounts on the platform.</p>
        </div>
        <a href="{{ route('admin.tenants.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Tenant
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-4">
        <form action="{{ route('admin.tenants.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, slug, or email..."
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm">
            </div>
            <div class="w-full sm:w-40">
                <select name="status" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-surface-100 focus:border-brand-500 text-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                </select>
            </div>
            <div class="w-full sm:w-40">
                <select name="plan" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-surface-100 focus:border-brand-500 text-sm">
                    <option value="">All Plans</option>
                    <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                    <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                    <option value="business" {{ request('plan') === 'business' ? 'selected' : '' }}>Business</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-surface-700 text-white font-medium rounded-lg hover:bg-surface-600 transition-colors text-sm">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'plan']))
                <a href="{{ route('admin.tenants.index') }}" class="px-4 py-2 text-surface-400 hover:text-white transition-colors text-sm">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-700/50">
                    <tr class="text-left text-xs font-semibold text-surface-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Tenant</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">Plan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Stats</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-surface-700/30">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-brand-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-brand-400 font-semibold">{{ strtoupper(substr($tenant->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $tenant->name }}</p>
                                    <p class="text-surface-500 text-xs">{{ $tenant->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-surface-300 text-sm">{{ $tenant->owner->name ?? 'N/A' }}</p>
                            <p class="text-surface-500 text-xs">{{ $tenant->owner->email ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $planColors = [
                                    'free' => 'bg-surface-500/10 text-surface-400',
                                    'pro' => 'bg-brand-500/10 text-brand-400',
                                    'business' => 'bg-amber-500/10 text-amber-400',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $planColors[$tenant->plan] ?? '' }}">
                                {{ ucfirst($tenant->plan) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'active' => 'bg-emerald-500/10 text-emerald-400',
                                    'suspended' => 'bg-red-500/10 text-red-400',
                                    'trial' => 'bg-blue-500/10 text-blue-400',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$tenant->status] ?? '' }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-surface-400 space-y-0.5">
                                <p>{{ $tenant->brands_count ?? 0 }} brands</p>
                                <p>{{ $tenant->invoices_count ?? 0 }} invoices</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm">
                            {{ $tenant->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="p-1.5 text-surface-400 hover:text-white transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="p-1.5 text-surface-400 hover:text-white transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.tenants.impersonate', $tenant) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-surface-400 hover:text-brand-400 transition-colors" title="Login as">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    </button>
                                </form>
                                @if($tenant->status === 'active')
                                    <form action="{{ route('admin.tenants.suspend', $tenant) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-surface-400 hover:text-amber-400 transition-colors" title="Suspend" onclick="return confirm('Are you sure you want to suspend this tenant?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.tenants.activate', $tenant) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-surface-400 hover:text-emerald-400 transition-colors" title="Activate">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-surface-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                            <p>No tenants found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($tenants->hasPages())
    <div class="flex justify-center">
        {{ $tenants->links() }}
    </div>
    @endif
</div>
@endsection