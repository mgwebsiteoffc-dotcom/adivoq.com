@extends('layouts.admin')
@section('title', $tenant->name)
@section('page_title', 'Tenant: ' . $tenant->name)

@section('content')
{{-- Header Actions --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.tenants.index') }}" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        <span class="inline-block px-3 py-1 text-xs font-bold rounded-full capitalize
            {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ $tenant->status }}
        </span>
        <span class="inline-block px-3 py-1 text-xs font-bold rounded-full capitalize
            {{ $tenant->plan === 'free' ? 'bg-gray-100 text-gray-600' : 'bg-indigo-100 text-indigo-700' }}">
            {{ $tenant->plan }} plan
        </span>
    </div>
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.tenants.impersonate', $tenant) }}" class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700">
            <i class="fas fa-sign-in-alt mr-1"></i>Login As
        </a>
        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700">
            <i class="fas fa-edit mr-1"></i>Edit
        </a>
        @if($tenant->status === 'active')
            <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" onsubmit="return confirm('Suspend?')">
                @csrf
                <button class="px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700">
                    <i class="fas fa-ban mr-1"></i>Suspend
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.tenants.reactivate', $tenant) }}">@csrf
                <button class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700">Reactivate</button>
            </form>
        @endif
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Invoices', 'value' => $stats['total_invoices'], 'icon' => 'fa-file-invoice'],
        ['label' => 'Paid Invoices', 'value' => $stats['paid_invoices'], 'icon' => 'fa-check-circle'],
        ['label' => 'Total Revenue', 'value' => '₹' . number_format($stats['total_revenue']), 'icon' => 'fa-indian-rupee-sign'],
        ['label' => 'Brands', 'value' => $stats['brands_count'], 'icon' => 'fa-building'],
        ['label' => 'Campaigns', 'value' => $stats['campaigns_count'], 'icon' => 'fa-bullhorn'],
        ['label' => 'Users', 'value' => $stats['users_count'], 'icon' => 'fa-users'],
    ] as $s)
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <i class="fas {{ $s['icon'] }} text-indigo-400 mb-2"></i>
            <p class="text-xl font-bold text-gray-900">{{ $s['value'] }}</p>
            <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Tenant Details --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Tenant Details</h3>
        <dl class="space-y-3 text-sm">
            @foreach([
                'Name' => $tenant->name,
                'Business' => $tenant->business_name ?: '—',
                'Email' => $tenant->email,
                'Phone' => $tenant->phone ?: '—',
                'GSTIN' => $tenant->gstin ?: '—',
                'PAN' => $tenant->pan_number ?: '—',
                'State' => $tenant->state ?: '—',
                'Plan' => ucfirst($tenant->plan),
                'Plan Status' => ucfirst($tenant->plan_status),
                'Trial Ends' => $tenant->trial_ends_at?->format('M d, Y') ?: '—',
                'Created' => $tenant->created_at->format('M d, Y h:i A'),
            ] as $label => $value)
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-500">{{ $label }}</span>
                    <span class="font-medium text-gray-900">{{ $value }}</span>
                </div>
            @endforeach
        </dl>
    </div>

    {{-- Team Members --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Team Members</h3>
        <div class="space-y-3">
            @foreach($tenant->users as $user)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                            <span class="text-indigo-600 font-bold text-xs">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 capitalize">{{ $user->role }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Invoices --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Invoices</h3>
        <div class="space-y-2">
            @forelse($recentInvoices as $inv)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $inv->invoice_number }}</p>
                        <p class="text-xs text-gray-500">{{ $inv->brand->name ?? '—' }} • {{ $inv->issue_date->format('M d') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold">₹{{ number_format($inv->total_amount, 2) }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $inv->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $inv->status === 'sent' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $inv->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                            {{ $inv->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                            {{ in_array($inv->status, ['partially_paid','viewed','cancelled']) ? 'bg-yellow-100 text-yellow-700' : '' }}">
                            {{ ucfirst($inv->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No invoices.</p>
            @endforelse
        </div>
    </div>

    {{-- Activity --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Activity Log</h3>
        <div class="space-y-2 max-h-72 overflow-y-auto">
            @forelse($activityLogs as $log)
                <div class="flex items-start py-2 border-b border-gray-50 last:border-0">
                    <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center mr-2 mt-0.5 flex-shrink-0">
                        <i class="fas fa-circle text-gray-300" style="font-size:4px;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-800"><span class="font-semibold text-indigo-600">{{ $log->action }}</span> {{ $log->description }}</p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No activity.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection