@extends('layouts.tenant')
@section('title','Expenses Report')
@section('page_title','Expenses Report')

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
            <a href="{{ route('dashboard.reports.expenses') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-black hover:bg-gray-200">Reset</a>
        @endif
    </form>

    <a href="{{ route('dashboard.reports.export', ['type' => 'expenses']) }}"
       class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-black hover:bg-green-700">
        <i class="fas fa-download mr-2"></i>Export Expenses CSV
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Expenses by Category</h3>
        <div class="h-72">
            <canvas id="expCatChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Category Totals</h3>
        <div class="space-y-3">
            @forelse($byCategory->take(12) as $row)
                <div class="flex justify-between border-b pb-2 last:border-0 last:pb-0">
                    <span class="text-sm font-semibold text-gray-700 truncate pr-2">{{ $row['category'] }}</span>
                    <span class="text-sm font-black text-red-700">₹{{ number_format($row['total'],2) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">No expenses.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Title</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Category</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($expenses as $e)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $e->expense_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-bold text-gray-900">{{ $e->title }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $e->category->name ?? 'Uncategorized' }}</td>
                        <td class="px-4 py-3 text-right font-black text-red-700">₹{{ number_format($e->amount,2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-14 text-center text-gray-500">No expenses.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t">{{ $expenses->links() }}</div>
</div>

@push('scripts')
<script>
(function () {
    const labels = {!! json_encode($byCategory->pluck('category')) !!};
    const data = {!! json_encode($byCategory->pluck('total')) !!};

    const ctx = document.getElementById('expCatChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Expenses',
                data,
                backgroundColor: 'rgba(239,68,68,0.75)',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => '₹' + v.toLocaleString('en-IN') } },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>
@endpush
@endsection