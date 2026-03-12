<!-- resources/views/admin/tenants/show.blade.php -->
@extends('layouts.admin')

@section('title', $tenant->name)
@section('page_title', $tenant->name)

@section('content')
<div class="space-y-6">
    {{-- Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center gap-2 text-surface-400 hover:text-white text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Tenants
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="px-4 py-2 bg-surface-700 text-white font-medium rounded-lg hover:bg-surface-600 transition-colors text-sm">
                Edit
            </a>
            <form action="{{ route('admin.tenants.impersonate', $tenant) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
                    Login as Owner
                </button>
            </form>
        </div>
    </div>

    {{-- Header Card --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
        <div class="flex items-start gap-6">
            <div class="w-16 h-16 bg-brand-600/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="text-brand-400 text-2xl font-bold">{{ strtoupper(substr($tenant->name, 0, 1)) }}</span>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-white">{{ $tenant->name }}</h2>
                    @php
                        $statusColors = [
                            'active' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                            'suspended' => 'bg-red-500/10 text-red-400 border-red-500/20',
                            'trial' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusColors[$tenant->status] ?? '' }}">
                        {{ ucfirst($tenant->status) }}
                    </span>
                </div>
                <p class="text-surface-400 text-sm">Slug: {{ $tenant->slug }}</p>
                <p class="text-surface-400 text-sm">Plan: <span class="text-brand-400 font-medium">{{ ucfirst($tenant->plan) }}</span></p>
                <p class="text-surface-500 text-sm mt-2">Created {{ $tenant->created_at->format('M d, Y') }} ({{ $tenant->created_at->diffForHumans() }})</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <p class="text-surface-400 text-sm">Total Revenue</p>
            <p class="text-2xl font-bold text-white mt-1">₹{{ number_format($stats['total_revenue'], 0) }}</p>
        </div>
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <p class="text-surface-400 text-sm">Invoices</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['total_invoices'] }}</p>
        </div>
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <p class="text-surface-400 text-sm">Brands</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['total_brands'] }}</p>
        </div>
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
            <p class="text-surface-400 text-sm">Users</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['total_users'] }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Owner Info --}}
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Owner Details</h3>
            @if($tenant->owner)
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-surface-400 text-sm">Name</span>
                    <span class="text-white text-sm">{{ $tenant->owner->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-400 text-sm">Email</span>
                    <span class="text-white text-sm">{{ $tenant->owner->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-400 text-sm">Phone</span>
                    <span class="text-white text-sm">{{ $tenant->owner->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-400 text-sm">Last Login</span>
                    <span class="text-white text-sm">{{ $tenant->owner->last_login_at?->diffForHumans() ?? 'Never' }}</span>
                </div>
            </div>
            @else
            <p class="text-surface-500 text-sm">No owner assigned</p>
            @endif
        </div>

        {{-- Recent Invoices --}}
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Recent Invoices</h3>
            @if($tenant->invoices->count())
            <div class="space-y-3">
                @foreach($tenant->invoices as $invoice)
                <div class="flex justify-between items-center py-2 border-b border-surface-700 last:border-0">
                    <div>
                        <p class="text-white text-sm font-medium">{{ $invoice->invoice_number }}</p>
                        <p class="text-surface-500 text-xs">{{ $invoice->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-white text-sm font-mono">₹{{ number_format($invoice->total_amount, 0) }}</p>
                        @php
                            $invStatusColors = [
                                'paid' => 'text-emerald-400',
                                'sent' => 'text-blue-400',
                                'draft' => 'text-surface-400',
                                'overdue' => 'text-red-400',
                            ];
                        @endphp
                        <p class="text-xs {{ $invStatusColors[$invoice->status] ?? 'text-surface-400' }}">{{ ucfirst($invoice->status) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-surface-500 text-sm">No invoices yet</p>
            @endif
        </div>
    </div>
</div>
@endsection