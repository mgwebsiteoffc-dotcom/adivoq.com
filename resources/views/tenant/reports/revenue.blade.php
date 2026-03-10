@extends('layouts.tenant')
@section('title','Revenue Report')
@section('page_title','Revenue Report')

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
            <a href="{{ route('dashboard.reports.revenue') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-black hover:bg-gray-200">Reset</a>
        @endif
    </form>

    <div class="flex gap-2">
        <a href="{{ route('dashboard.reports.export', ['type' => 'revenue'] ) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-black hover:bg-green-700">
            <i class="fas fa-download mr-2"></i>Export Payments CSV
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Revenue by Month</h3>
        <div class="h-72">
            <canvas id="revChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Top Brands</h3>
        <div class="space-y-3">
            @forelse($brandBreakdown->take(10) as $row)
                <div class="flex items-center justify-between border-b pb-2 last:border-0 last:pb-0">
                    <span class="text-sm font-semibold text-gray-700 truncate pr-2">{{ $row['brand'] }}</span>
                    <span class="text-sm font-black text-green-700">₹{{ number_format($row['revenue']) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">No data.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const labels = {!! json_encode($byMonth->keys()->map(fn($k) => \Carbon\Carbon::createFromFormat('Y-m', $k)->format('M Y'))->values()) !!};
    const data = {!! json_encode($byMonth->values()->values()) !!};

    const ctx = document.getElementById('revChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Revenue',
                data,
                backgroundColor: 'rgba(34,197,94,0.85)',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => '₹' + v.toLocaleString('en-IN') }
                },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>
@endpush
@endsection