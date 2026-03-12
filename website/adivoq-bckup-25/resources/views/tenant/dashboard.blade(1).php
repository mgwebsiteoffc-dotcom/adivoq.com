@extends('layouts.tenant')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Welcome --}}
    <div class="bg-gradient-to-r from-brand-600/20 to-indigo-600/20 border border-brand-500/20 rounded-xl p-6">
        <h2 class="text-xl font-semibold text-white">
            Welcome back, {{ auth()->user()->name }} 👋
        </h2>
        <p class="text-surface-400 mt-1">
            Here's what's happening with your creator business.
        </p>
    </div>

    {{-- Stats (Full Width) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
            <p class="text-surface-400 text-sm">Total Revenue</p>
            <p class="text-2xl font-bold text-green-400 mt-1">
                ₹{{ number_format($stats['revenue'], 0) }}
            </p>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
            <p class="text-surface-400 text-sm">Expenses</p>
            <p class="text-2xl font-bold text-red-400 mt-1">
                ₹{{ number_format($stats['expenses'], 0) }}
            </p>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
            <p class="text-surface-400 text-sm">Net Profit</p>
            <p class="text-2xl font-bold {{ $stats['profit'] >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                ₹{{ number_format($stats['profit'], 0) }}
            </p>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
            <p class="text-surface-400 text-sm">Pending Amount</p>
            <p class="text-2xl font-bold text-amber-400 mt-1">
                ₹{{ number_format($stats['pending'], 0) }}
            </p>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
            <p class="text-surface-400 text-sm">Overdue</p>
            <p class="text-2xl font-bold text-red-400 mt-1">
                {{ $overdueCount }}
            </p>
        </div>

    </div>

    {{-- Row 2: Quick Actions (6 col) + Recent Invoice (6 col) --}}
    <div class="grid grid-cols-12 gap-6">

        {{-- Quick Actions (6 cols) --}}
        <div class="col-span-12 lg:col-span-6 bg-surface-800 border border-surface-700 rounded-xl p-6">

            <h3 class="text-lg font-semibold text-white mb-4">
                Quick Actions
            </h3>

            <div class="grid grid-cols-2 gap-4">

                <a href="{{ route('tenant.invoices.create') }}"
                   class="flex flex-col items-center justify-center p-4 bg-surface-700/50 rounded-lg hover:bg-surface-700 transition">
                    <span class="text-2xl">🧾</span>
                    <span class="text-sm text-surface-300 mt-2">New Invoice</span>
                </a>

                <a href="{{ route('tenant.brands.index') }}"
                   class="flex flex-col items-center justify-center p-4 bg-surface-700/50 rounded-lg hover:bg-surface-700 transition">
                    <span class="text-2xl">🏢</span>
                    <span class="text-sm text-surface-300 mt-2">Brands</span>
                </a>

                <a href="{{ route('tenant.campaigns.index') }}"
                   class="flex flex-col items-center justify-center p-4 bg-surface-700/50 rounded-lg hover:bg-surface-700 transition">
                    <span class="text-2xl">🎯</span>
                    <span class="text-sm text-surface-300 mt-2">Campaigns</span>
                </a>

                <a href="{{ route('tenant.expenses') }}"
                   class="flex flex-col items-center justify-center p-4 bg-surface-700/50 rounded-lg hover:bg-surface-700 transition">
                    <span class="text-2xl">💸</span>
                    <span class="text-sm text-surface-300 mt-2">Expenses</span>
                </a>

            </div>
        </div>

        {{-- Recent Invoices (6 cols) --}}
        <div class="col-span-12 lg:col-span-6 bg-surface-800 border border-surface-700 rounded-xl p-6">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Recent Invoices</h3>
                <a href="{{ route('tenant.invoices.index') }}"
                   class="text-sm text-brand-400 hover:text-brand-300">
                    View all →
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentInvoices as $invoice)
                    <div class="flex justify-between p-3 bg-surface-700/30 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-white">
                                {{ $invoice->invoice_number }}
                            </p>
                            <p class="text-xs text-surface-400">
                                {{ $invoice->brand->name ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-white">
                                ₹{{ number_format($invoice->total_amount, 0) }}
                            </p>
                            <p class="text-xs
                                @if($invoice->display_status == 'paid') text-green-400
                                @elseif($invoice->display_status == 'overdue') text-red-400
                                @else text-amber-400
                                @endif">
                                {{ ucfirst($invoice->display_status) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-surface-500 text-sm">No invoices yet</p>
                @endforelse
            </div>

        </div>

    </div>

    {{-- Row 3: Monthly Revenue (6 col centered) --}}
    <div class="grid grid-cols-12">
        <div class="col-span-12 lg:col-span-6 lg:col-start-4 bg-surface-800 border border-surface-700 rounded-xl p-6">

            <h3 class="text-lg font-semibold text-white mb-4">
                Monthly Revenue
            </h3>

            <canvas id="revenueChart"></canvas>

        </div>
    </div>
    
    <div class="grid grid-cols-12 gap-6 mt-8">

    <div class="col-span-12 lg:col-span-6 bg-surface-800 border border-surface-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            Revenue vs Expenses
        </h3>
        <canvas id="profitChart"></canvas>
    </div>

    <div class="col-span-12 lg:col-span-6 bg-surface-800 border border-surface-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            Brand Revenue Breakdown
        </h3>
        <canvas id="brandChart"></canvas>
    </div>

</div>
<div class="bg-surface-800 border border-surface-700 rounded-xl p-6 mt-8">
    <h3 class="text-lg font-semibold text-white mb-4">
        Invoice Aging Summary
    </h3>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">

        <div>
            <p class="text-surface-400 text-sm">Current</p>
            <p class="text-white font-semibold mt-1">
                ₹{{ number_format($aging['current'],0) }}
            </p>
        </div>

        <div>
            <p class="text-surface-400 text-sm">0–30 Days</p>
            <p class="text-yellow-400 font-semibold mt-1">
                ₹{{ number_format($aging['30'],0) }}
            </p>
        </div>

        <div>
            <p class="text-surface-400 text-sm">31–60 Days</p>
            <p class="text-orange-400 font-semibold mt-1">
                ₹{{ number_format($aging['60'],0) }}
            </p>
        </div>

        <div>
            <p class="text-surface-400 text-sm">60+ Days</p>
            <p class="text-red-400 font-semibold mt-1">
                ₹{{ number_format($aging['90'],0) }}
            </p>
        </div>

    </div>
</div>

</div>

{{-- Chart --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($monthlyRevenue->toArray())) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode(array_values($monthlyRevenue->toArray())) !!},
            backgroundColor: '#8b5cf6'
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { ticks: { color: '#aaa' } },
            y: { ticks: { color: '#aaa' } }
        }
    }
});


</script>

@endsection