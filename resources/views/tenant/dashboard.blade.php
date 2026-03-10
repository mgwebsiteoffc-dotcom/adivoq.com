@extends('layouts.tenant')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Welcome back, ' . auth()->user()->name . '!')

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase">Revenue This Month</span>
            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-indian-rupee-sign text-green-600 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">₹{{ number_format($revenueThisMonth) }}</p>
        <p class="text-xs mt-1 {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
            <i class="fas fa-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>{{ abs($revenueGrowth) }}% vs last month
        </p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase">Outstanding</span>
            <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-orange-600 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">₹{{ number_format($outstandingAmount) }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $pendingPayments }} pending invoices</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition {{ $overdueCount > 0 ? 'border-red-200' : '' }}">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase">Overdue</span>
            <div class="w-9 h-9 {{ $overdueCount > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle {{ $overdueCount > 0 ? 'text-red-600' : 'text-gray-400' }} text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold {{ $overdueCount > 0 ? 'text-red-600' : 'text-gray-900' }}">₹{{ number_format($overdueAmount) }}</p>
        <p class="text-xs {{ $overdueCount > 0 ? 'text-red-500' : 'text-gray-500' }} mt-1">{{ $overdueCount }} overdue invoices</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase">Active Campaigns</span>
            <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-bullhorn text-indigo-600 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $activeCampaigns }}</p>
        <p class="text-xs text-gray-500 mt-1">Year revenue: ₹{{ number_format($revenueThisYear) }}</p>
    </div>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
    @foreach([
        ['route' => 'dashboard.invoices.create', 'icon' => 'fa-file-invoice', 'label' => 'New Invoice', 'color' => 'indigo'],
        ['route' => 'dashboard.brands.create', 'icon' => 'fa-building', 'label' => 'Add Brand', 'color' => 'blue'],
        ['route' => 'dashboard.campaigns.create', 'icon' => 'fa-bullhorn', 'label' => 'New Campaign', 'color' => 'purple'],
        ['route' => 'dashboard.reports.index', 'icon' => 'fa-chart-bar', 'label' => 'View Reports', 'color' => 'green'],
    ] as $action)
        <a href="{{ route($action['route']) }}" class="flex items-center px-4 py-3 bg-{{ $action['color'] }}-50 border border-{{ $action['color'] }}-100 rounded-xl text-sm font-medium text-{{ $action['color'] }}-700 hover:bg-{{ $action['color'] }}-100 transition">
            <i class="fas {{ $action['icon'] }} mr-2"></i>{{ $action['label'] }}
        </a>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Revenue Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Revenue Trend — Last 6 Months</h3>
        <canvas id="revenueChart" height="200"></canvas>
    </div>

    {{-- Invoice Breakdown --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Invoice Status</h3>
        <div class="space-y-4">
            @php
                $statusConfig = [
                    'draft' => ['label' => 'Draft', 'color' => 'gray', 'icon' => 'fa-pencil-alt'],
                    'sent' => ['label' => 'Sent', 'color' => 'blue', 'icon' => 'fa-paper-plane'],
                    'paid' => ['label' => 'Paid', 'color' => 'green', 'icon' => 'fa-check-circle'],
                    'overdue' => ['label' => 'Overdue', 'color' => 'red', 'icon' => 'fa-exclamation-circle'],
                    'partially_paid' => ['label' => 'Partial', 'color' => 'yellow', 'icon' => 'fa-adjust'],
                ];
                $totalInvoices = max(array_sum($invoiceBreakdown), 1);
            @endphp
            @foreach($statusConfig as $status => $config)
                @php $count = $invoiceBreakdown[$status] ?? 0; $pct = round(($count / $totalInvoices) * 100); @endphp
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-{{ $config['color'] }}-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas {{ $config['icon'] }} text-{{ $config['color'] }}-600 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700">{{ $config['label'] }}</span>
                            <span class="text-gray-500">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-{{ $config['color'] }}-500 h-1.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Invoices --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Recent Invoices</h3>
            <a href="{{ route('dashboard.invoices.index') }}" class="text-xs text-indigo-600 font-medium hover:underline">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentInvoices as $inv)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center min-w-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-file-invoice text-indigo-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('dashboard.invoices.show', $inv) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600 truncate block">{{ $inv->invoice_number }}</a>
                            <p class="text-xs text-gray-500 truncate">{{ $inv->brand->name ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-3">
                        <p class="text-sm font-semibold text-gray-900">₹{{ number_format($inv->total_amount) }}</p>
                        @php
                            $sc = ['draft' => 'gray', 'sent' => 'blue', 'paid' => 'green', 'overdue' => 'red', 'partially_paid' => 'yellow', 'viewed' => 'purple', 'cancelled' => 'gray'];
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $sc[$inv->status] ?? 'gray' }}-100 text-{{ $sc[$inv->status] ?? 'gray' }}-700 font-medium">
                            {{ ucfirst(str_replace('_', ' ', $inv->status)) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-6">
                    <i class="fas fa-file-invoice text-gray-300 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500">No invoices yet</p>
                    <a href="{{ route('dashboard.invoices.create') }}" class="text-xs text-indigo-600 font-medium mt-1 inline-block">Create your first invoice →</a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Upcoming Milestones --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Upcoming Milestones</h3>
            <a href="{{ route('dashboard.campaigns.index') }}" class="text-xs text-indigo-600 font-medium hover:underline">View Campaigns →</a>
        </div>
        <div class="space-y-3">
            @forelse($upcomingMilestones as $ms)
                <div class="flex items-start py-2 border-b border-gray-50 last:border-0">
                    <div class="w-8 h-8 {{ $ms->due_date->isToday() ? 'bg-red-100' : ($ms->due_date->diffInDays(now()) <= 3 ? 'bg-orange-100' : 'bg-green-100') }} rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                        <i class="fas fa-flag {{ $ms->due_date->isToday() ? 'text-red-600' : ($ms->due_date->diffInDays(now()) <= 3 ? 'text-orange-600' : 'text-green-600') }} text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $ms->title }}</p>
                        <p class="text-xs text-gray-500">{{ $ms->campaign->brand->name ?? '' }} • {{ $ms->campaign->name ?? '' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-3">
                        <p class="text-xs font-medium {{ $ms->due_date->isToday() ? 'text-red-600' : 'text-gray-600' }}">
                            {{ $ms->due_date->isToday() ? 'Today' : $ms->due_date->format('M d') }}
                        </p>
                        @if($ms->amount > 0)
                            <p class="text-xs text-gray-500">₹{{ number_format($ms->amount) }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-6">
                    <i class="fas fa-flag text-gray-300 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500">No upcoming milestones</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($revenueChart)->pluck('month')) !!},
        datasets: [
            {
                label: 'Revenue Received',
                data: {!! json_encode(collect($revenueChart)->pluck('revenue')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderRadius: 6,
                barPercentage: 0.4,
            },
            {
                label: 'Invoiced',
                data: {!! json_encode(collect($revenueChart)->pluck('invoiced')) !!},
                backgroundColor: 'rgba(99, 102, 241, 0.3)',
                borderRadius: 6,
                barPercentage: 0.4,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '₹' + (v >= 1000 ? (v/1000) + 'K' : v) } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
@endsection