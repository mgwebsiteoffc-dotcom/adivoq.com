@extends('layouts.tenant')

@section('title','Reports')
@section('page_title','Reports')

@section('content')

<div class="space-y-8">

    {{-- Filter --}}
    <form method="GET"
          action="{{ route('tenant.reports') }}"
          class="glass rounded-2xl p-6 flex gap-4 items-end">

        <div>
            <label class="text-sm text-surface-400">Start Date</label>
            <input type="date" name="start_date"
                   value="{{ $start }}"
                   class="bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-white text-sm">
        </div>

        <div>
            <label class="text-sm text-surface-400">End Date</label>
            <input type="date" name="end_date"
                   value="{{ $end }}"
                   class="bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-white text-sm">
        </div>

        <button class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
            Filter
        </button>

        <a href="{{ route('tenant.reports.export',['start_date'=>$start,'end_date'=>$end]) }}"
           class="px-6 py-2 bg-surface-700 text-white rounded-lg text-sm hover:bg-surface-600">
           Export CSV
        </a>
        
        <a href="{{ route('tenant.reports.pnl.pdf', ['start_date'=>$start,'end_date'=>$end]) }}"
   class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
   Download P&L PDF
</a>

<a href="{{ route('tenant.reports.gst') }}"
   class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
   GST Summary
</a>

<a href="{{ route('tenant.reports.tax-summary') }}"
   class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
   Tax Summary
</a>

    </form>

    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">

        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-surface-400 uppercase">Revenue</p>
            <p class="text-2xl text-green-400 font-bold mt-2">
                ₹{{ number_format($report['total_revenue'],0) }}
            </p>
        </div>

        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-surface-400 uppercase">Expenses</p>
            <p class="text-2xl text-red-400 font-bold mt-2">
                ₹{{ number_format($report['total_expenses'],0) }}
            </p>
        </div>

        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-surface-400 uppercase">Profit</p>
            <p class="text-2xl {{ $report['profit'] >= 0 ? 'text-green-400' : 'text-red-400' }} font-bold mt-2">
                ₹{{ number_format($report['profit'],0) }}
            </p>
        </div>

        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-surface-400 uppercase">Invoices</p>
            <p class="text-2xl text-white font-bold mt-2">
                {{ $report['total_invoices'] }}
            </p>
        </div>

        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-surface-400 uppercase">Pending</p>
            <p class="text-2xl text-amber-400 font-bold mt-2">
                ₹{{ number_format($report['pending_amount'],0) }}
            </p>
        </div>

    </div>

    {{-- Brand Revenue Chart --}}
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg text-white font-semibold mb-6">
            Brand Revenue Breakdown
        </h3>

        <canvas id="brandRevenueChart"></canvas>
    </div>

</div>

<div class="glass rounded-2xl p-6 mt-8">
    <h3 class="text-lg font-semibold text-white mb-6">
        Detailed Aging Report
    </h3>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-surface-400 border-b border-surface-700">
                <tr>
                    <th class="py-3 text-left">Invoice</th>
                    <th class="py-3 text-left">Brand</th>
                    <th class="py-3 text-left">Due Date</th>
                    <th class="py-3 text-right">Days</th>
                    <th class="py-3 text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agingDetails as $item)
                <tr class="border-b border-surface-700">
                    <td class="py-3 text-white">
                        {{ $item['invoice_number'] }}
                    </td>
                    <td class="py-3 text-surface-300">
                        {{ $item['brand'] }}
                    </td>
                    <td class="py-3 text-surface-300">
                        {{ $item['due_date'] }}
                    </td>
                    <td class="py-3 text-right
                        @if($item['days'] < 0) text-red-400 @else text-green-400 @endif">
                        {{ $item['days'] < 0 ? abs($item['days']) . ' overdue' : 'Due in ' . $item['days'] }}
                    </td>
                    <td class="py-3 text-right text-amber-400 font-semibold">
                        ₹{{ number_format($item['balance'],0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
#brandRevenueChart {
    height: 400px !important;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const canvas = document.getElementById('brandRevenueChart');

    if (!canvas) {
        console.log('Canvas not found');
        return;
    }

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($brandRevenue->toArray())) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode(array_values($brandRevenue->toArray())) !!},
                backgroundColor: '#8b5cf6',
                borderRadius: 6
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
            },
            scales: {
                x: {
                    ticks: { color: '#aaa' },
                    grid: { display: false }
                },
                y: {
                    ticks: { color: '#aaa' },
                    grid: { color: 'rgba(255,255,255,0.05)' }
                }
            }
        }
    });

});
</script>
@endsection