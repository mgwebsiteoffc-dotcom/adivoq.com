@extends('layouts.tenant')
@section('title','Profit & Loss')
@section('page_title','Profit & Loss')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-6">
    <form method="GET" class="bg-white rounded-xl border p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from', $from->toDateString()) }}" class="px-3 py-2 border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to', $to->toDateString()) }}" class="px-3 py-2 border rounded-lg text-sm">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">Apply</button>
        @if(request()->query())
            <a href="{{ route('dashboard.reports.profit-loss') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-black hover:bg-gray-200">Reset</a>
        @endif
    </form>

    <div class="bg-white rounded-xl border p-4 flex flex-wrap gap-6">
        <div>
            <p class="text-xs text-gray-500 font-semibold uppercase">Total Revenue</p>
            <p class="text-lg font-black text-green-700">₹{{ number_format($totals['revenue'],2) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-semibold uppercase">Total Expenses</p>
            <p class="text-lg font-black text-red-700">₹{{ number_format($totals['expense'],2) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-semibold uppercase">Profit</p>
            <p class="text-lg font-black {{ $totals['profit'] >= 0 ? 'text-indigo-700' : 'text-red-700' }}">
                ₹{{ number_format($totals['profit'],2) }}
            </p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border p-6 mb-6">
    <h3 class="text-sm font-black text-gray-900 mb-4">Monthly P&L</h3>
    <div class="h-72"><canvas id="plChart"></canvas></div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Month</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Revenue</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Expense</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Profit</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($months as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-bold text-gray-900">{{ $m['month'] }}</td>
                        <td class="px-4 py-3 text-right font-black text-green-700">₹{{ number_format($m['revenue'],2) }}</td>
                        <td class="px-4 py-3 text-right font-black text-red-700">₹{{ number_format($m['expense'],2) }}</td>
                        <td class="px-4 py-3 text-right font-black {{ $m['profit']>=0 ? 'text-indigo-700' : 'text-red-700' }}">₹{{ number_format($m['profit'],2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
(function(){
    const labels = {!! json_encode(collect($months)->pluck('month')) !!};
    const revenue = {!! json_encode(collect($months)->pluck('revenue')) !!};
    const expense = {!! json_encode(collect($months)->pluck('expense')) !!};
    const profit = {!! json_encode(collect($months)->pluck('profit')) !!};

    const ctx = document.getElementById('plChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label: 'Revenue', data: revenue, borderColor: 'rgba(34,197,94,0.9)', backgroundColor: 'rgba(34,197,94,0.1)', fill: true, tension: 0.25 },
                { label: 'Expense', data: expense, borderColor: 'rgba(239,68,68,0.9)', backgroundColor: 'rgba(239,68,68,0.1)', fill: true, tension: 0.25 },
                { label: 'Profit', data: profit, borderColor: 'rgba(99,102,241,0.95)', backgroundColor: 'rgba(99,102,241,0.08)', fill: false, tension: 0.25 },
            ]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } }, scales:{ y:{ beginAtZero:true } } }
    });
})();
</script>
@endpush
@endsection