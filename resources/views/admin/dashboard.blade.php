@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page_title', 'Admin Dashboard')

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Tenants', 'value' => number_format($stats['total_tenants']), 'icon' => 'fa-users', 'color' => 'bg-indigo-500', 'sub' => $stats['active_tenants'] . ' active'],
        ['label' => 'MRR', 'value' => '₹' . number_format($stats['mrr']), 'icon' => 'fa-chart-line', 'color' => 'bg-green-500', 'sub' => 'ARR: ₹' . number_format($stats['arr'])],
        ['label' => 'New This Month', 'value' => number_format($stats['new_signups_month']), 'icon' => 'fa-user-plus', 'color' => 'bg-blue-500', 'sub' => $stats['new_signups_today'] . ' today'],
        ['label' => 'Unread Messages', 'value' => $stats['unread_messages'], 'icon' => 'fa-envelope', 'color' => 'bg-red-500', 'sub' => $stats['waitlist_count'] . ' on waitlist'],
    ] as $card)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</span>
                <div class="w-9 h-9 {{ $card['color'] }} rounded-lg flex items-center justify-center">
                    <i class="fas {{ $card['icon'] }} text-white text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $card['sub'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Signup Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Signups — Last 30 Days</h3>
        <div class="h-56">
            <canvas id="signupChart"></canvas>
        </div>
    </div>

    {{-- Plan Distribution --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Plan Distribution</h3>
        <div class="space-y-4">
            @php
                $planColors = ['free' => 'bg-gray-400', 'starter' => 'bg-blue-500', 'professional' => 'bg-indigo-500', 'enterprise' => 'bg-purple-500'];
                $total = max(array_sum($planDistribution), 1);
            @endphp
            @foreach(['free', 'starter', 'professional', 'enterprise'] as $plan)
                @php $count = $planDistribution[$plan] ?? 0; $pct = round(($count / $total) * 100); @endphp
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700 capitalize">{{ $plan }}</span>
                        <span class="text-gray-500">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="{{ $planColors[$plan] }} h-2.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Tenants --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Recent Tenants</h3>
            <a href="{{ route('admin.tenants.index') }}" class="text-xs text-indigo-600 font-medium hover:underline">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentTenants as $tenant)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                            <span class="text-indigo-600 font-bold text-xs">{{ substr($tenant->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $tenant->name }}</a>
                            <p class="text-xs text-gray-500">{{ $tenant->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $tenant->plan === 'free' ? 'bg-gray-100 text-gray-600' : '' }}
                            {{ $tenant->plan === 'starter' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $tenant->plan === 'professional' ? 'bg-indigo-100 text-indigo-700' : '' }}
                            {{ $tenant->plan === 'enterprise' ? 'bg-purple-100 text-purple-700' : '' }}">
                            {{ ucfirst($tenant->plan) }}
                        </span>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $tenant->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No tenants yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Recent Activity</h3>
            <a href="{{ route('admin.activity-logs.index') }}" class="text-xs text-indigo-600 font-medium hover:underline">View All →</a>
        </div>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            @forelse($recentActivity as $log)
                <div class="flex items-start py-2 border-b border-gray-50 last:border-0">
                    <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                        <i class="fas fa-circle text-gray-400" style="font-size: 6px;"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm text-gray-800">
                            <span class="font-medium text-indigo-600">{{ $log->action }}</span>
                            @if($log->description) — {{ Str::limit($log->description, 60) }}@endif
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $log->created_at->diffForHumans() }}
                            @if($log->tenant) • {{ $log->tenant->name }}@endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No activity yet.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('signupChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($signupChart)->pluck('date')) !!},
        datasets: [{
            label: 'New Signups',
            data: {!! json_encode(collect($signupChart)->pluck('count')) !!},
            backgroundColor: 'rgba(99, 102, 241, 0.8)',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { ticks: { maxTicksLimit: 10, font: { size: 10 } } }
        }
    }
});
</script>
@endpush
@endsection