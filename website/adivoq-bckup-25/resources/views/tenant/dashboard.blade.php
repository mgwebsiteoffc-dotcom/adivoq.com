@extends('layouts.tenant')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

<style>
.glass {
    background: rgba(30,41,59,0.65);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(148,163,184,0.15);
}
.card-hover {
    transition: all .25s ease;
}
.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(139,92,246,0.15);
}
.soft-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(148,163,184,0.2), transparent);
}
</style>

<div class="space-y-10">

    {{-- Welcome --}}
    <div class="glass rounded-2xl p-8 shadow-lg">
        <h2 class="text-2xl font-semibold text-white">
            Welcome back, {{ auth()->user()->name }} 👋
        </h2>
        <p class="text-surface-400 mt-3 text-sm">
            Your financial performance overview.
        </p>
    </div>
    
    @if($advanceTaxWarning)
<div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl p-4">
    ⚠️ Your estimated tax liability exceeds ₹10,000. 
    Advance tax payment may be required this quarter.
</div>
@endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">

        <div class="glass card-hover rounded-2xl p-6">
            <p class="text-surface-400 text-xs uppercase tracking-wide">Revenue</p>
            <p class="text-2xl font-bold text-green-400 mt-4">
                ₹{{ number_format($stats['revenue'], 0) }}
            </p>
        </div>

        <div class="glass card-hover rounded-2xl p-6">
            <p class="text-surface-400 text-xs uppercase tracking-wide">Expenses</p>
            <p class="text-2xl font-bold text-red-400 mt-4">
                ₹{{ number_format($stats['expenses'], 0) }}
            </p>
        </div>

        <div class="glass card-hover rounded-2xl p-6">
            <p class="text-surface-400 text-xs uppercase tracking-wide">Net Profit</p>
            <p class="text-2xl font-bold {{ $stats['profit'] >= 0 ? 'text-green-400' : 'text-red-400' }} mt-4">
                ₹{{ number_format($stats['profit'], 0) }}
            </p>
        </div>

        <div class="glass card-hover rounded-2xl p-6">
            <p class="text-surface-400 text-xs uppercase tracking-wide">Pending</p>
            <p class="text-2xl font-bold text-amber-400 mt-4">
                ₹{{ number_format($stats['pending'], 0) }}
            </p>
        </div>

        <div class="glass card-hover rounded-2xl p-6">
            <p class="text-surface-400 text-xs uppercase tracking-wide">Overdue</p>
            <p class="text-2xl font-bold text-red-400 mt-4">
                {{ $overdueCount }}
            </p>
        </div>

    </div>

    {{-- Quick Actions + Recent --}}
    <div class="grid grid-cols-12 gap-8">

        {{-- Quick Actions --}}
        <div class="col-span-12 lg:col-span-6 glass rounded-2xl p-8 shadow-md">

            <h3 class="text-lg font-semibold text-white mb-6">
                Quick Actions
            </h3>

            <div class="grid grid-cols-2 gap-6">

                <a href="{{ route('tenant.invoices.create') }}"
                   class="flex flex-col items-center justify-center gap-3 p-6 bg-surface-700/30 rounded-xl hover:bg-surface-700 transition">
                    <span class="text-3xl">🧾</span>
                    <span class="text-sm text-surface-300">New Invoice</span>
                </a>

                 <a href="{{ route('tenant.brands.index') }}"
                   class="flex flex-col items-center justify-center gap-3 p-6 bg-surface-700/30 rounded-xl hover:bg-surface-700 transition">
                    <span class="text-3xl">🏢</span>
                    <span class="text-sm text-surface-300">Brands</span>
                </a>

           <a href="{{ route('tenant.campaigns.index') }}"
                   class="flex flex-col items-center justify-center gap-3 p-6 bg-surface-700/30 rounded-xl hover:bg-surface-700 transition">
                    <span class="text-3xl">🎯</span>
                    <span class="text-sm text-surface-300">Campaigns</span>
                </a>

                <a href="{{ route('tenant.expenses') }}"
                   class="flex flex-col items-center justify-center gap-3 p-6 bg-surface-700/30 rounded-xl hover:bg-surface-700 transition">
                    <span class="text-3xl">💸</span>
                    <span class="text-sm text-surface-300">Expenses</span>
                </a>

            </div>

        </div>

        {{-- Recent Invoices --}}
        <div class="col-span-12 lg:col-span-6 glass rounded-2xl p-8 shadow-md">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-white">
                    Recent Invoices
                </h3>
                <a href="{{ route('tenant.invoices.index') }}"
                   class="text-sm text-brand-400 hover:text-brand-300">
                    View all →
                </a>
            </div>

            <div class="space-y-5">
                @forelse($recentInvoices as $invoice)
                    <div class="flex justify-between items-center p-5 bg-surface-700/30 rounded-xl">

                        <div>
                            <p class="text-sm font-medium text-white">
                                {{ $invoice->invoice_number }}
                            </p>
                            <p class="text-xs text-surface-400 mt-1">
                                {{ $invoice->brand->name ?? '-' }}
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="text-sm font-semibold text-white">
                                ₹{{ number_format($invoice->total_amount, 0) }}
                            </p>
                            <span class="text-xs mt-1 block
                                @if($invoice->display_status == 'paid') text-green-400
                                @elseif($invoice->display_status == 'overdue') text-red-400
                                @else text-amber-400
                                @endif">
                                {{ ucfirst($invoice->display_status) }}
                            </span>
                        </div>

                    </div>
                @empty
                    <p class="text-surface-500 text-sm">No invoices yet</p>
                @endforelse
            </div>

        </div>

    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-12 gap-8">

        <div class="col-span-12 lg:col-span-6 glass rounded-2xl p-8 shadow-md">
            <h3 class="text-lg font-semibold text-white mb-6">
                Monthly Revenue
            </h3>
            <canvas id="revenueChart"></canvas>
        </div>

        <div class="col-span-12 lg:col-span-6 glass rounded-2xl p-8 shadow-md">
            <h3 class="text-lg font-semibold text-white mb-6">
                Revenue vs Expenses
            </h3>
            <canvas id="profitChart"></canvas>
        </div>

    </div>
{{-- Brand Breakdown --}}
<div class="glass rounded-2xl p-8 shadow-md">
    <h3 class="text-lg font-semibold text-white mb-6">
        Brand Revenue Breakdown
    </h3>

    <div class="max-w-md mx-auto">
        <canvas id="brandChart"></canvas>
    </div>
</div>

    {{-- Aging --}}
    <div class="glass rounded-2xl p-8 shadow-md">
        <h3 class="text-lg font-semibold text-white mb-6">
            Invoice Aging Summary
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">

            <div>
                <p class="text-surface-400 text-xs uppercase">Current</p>
                <p class="text-white font-semibold mt-3 text-lg">
                    ₹{{ number_format($aging['current'],0) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-xs uppercase">0–30</p>
                <p class="text-yellow-400 font-semibold mt-3 text-lg">
                    ₹{{ number_format($aging['30'],0) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-xs uppercase">31–60</p>
                <p class="text-orange-400 font-semibold mt-3 text-lg">
                    ₹{{ number_format($aging['60'],0) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-xs uppercase">60+</p>
                <p class="text-red-400 font-semibold mt-3 text-lg">
                    ₹{{ number_format($aging['90'],0) }}
                </p>
            </div>

        </div>

    </div>

</div>
{{-- Charts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// Counter Animation
document.querySelectorAll('.counter').forEach(counter => {
    const update = () => {
        const target = +counter.getAttribute('data-target');
        const current = +counter.innerText.replace(/,/g,'');
        const increment = target / 60;

        if (current < target) {
            counter.innerText = Math.ceil(current + increment).toLocaleString();
            setTimeout(update, 20);
        } else {
            counter.innerText = target.toLocaleString();
        }
    };
    update();
});

// Charts
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode(array_keys($monthlyRevenue->toArray())) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode(array_values($monthlyRevenue->toArray())) !!},
            borderColor: '#8b5cf6',
            backgroundColor: 'rgba(139,92,246,0.2)',
            tension: 0.4,
            fill: true
        }]
    }
});

new Chart(document.getElementById('profitChart'), {
    type: 'bar',
    data: {
        labels: ['Revenue','Expenses'],
        datasets: [{
            data: [{{ $stats['revenue'] }},{{ $stats['expenses'] }}],
            backgroundColor: ['#22c55e','#ef4444']
        }]
    },
    options:{ plugins:{ legend:{ display:false } } }
});

new Chart(document.getElementById('brandChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($brandRevenue->toArray())) !!},
        datasets: [{
            data: {!! json_encode(array_values($brandRevenue->toArray())) !!},
            backgroundColor: [
                '#8b5cf6',
                '#3b82f6',
                '#22c55e',
                '#f59e0b',
                '#ef4444'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#fff'
                }
            }
        }
    }
});
</script>
@endsection