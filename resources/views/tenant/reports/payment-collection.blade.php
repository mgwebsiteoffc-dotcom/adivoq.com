@extends('layouts.tenant')
@section('title','Payment Collection')
@section('page_title','Payment Collection')

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
            <a href="{{ route('dashboard.reports.payment-collection') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-black hover:bg-gray-200">Reset</a>
        @endif
    </form>

    <a href="{{ route('dashboard.reports.export', ['type' => 'revenue']) }}"
       class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-black hover:bg-green-700">
        <i class="fas fa-download mr-2"></i>Export Payments CSV
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Payments by Method</h3>
        <div class="h-64">
            <canvas id="methodChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Method Totals</h3>
        <div class="space-y-3">
            @forelse($byMethod as $row)
                <div class="flex justify-between border-b pb-2 last:border-0 last:pb-0">
                    <span class="text-sm font-semibold text-gray-700">{{ ucfirst(str_replace('_',' ',$row->payment_method)) }}</span>
                    <span class="text-sm font-black text-green-700">₹{{ number_format($row->total,2) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">No payments.</p>
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
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Invoice</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Brand</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Method</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($payments as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $p->payment_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <a class="font-black text-indigo-600 hover:underline" href="{{ route('dashboard.invoices.show',$p->invoice) }}">
                                {{ $p->invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $p->invoice->brand->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</td>
                        <td class="px-4 py-3 text-right font-black text-green-700">₹{{ number_format($p->amount,2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-14 text-center text-gray-500">No payments.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t">{{ $payments->links() }}</div>
</div>

@push('scripts')
<script>
(function () {
    const labels = {!! json_encode($byMethod->pluck('payment_method')->map(fn($m) => ucfirst(str_replace('_',' ',$m))) ) !!};
    const data = {!! json_encode($byMethod->pluck('total')) !!};

    const ctx = document.getElementById('methodChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: [
                    'rgba(99,102,241,0.85)',
                    'rgba(34,197,94,0.85)',
                    'rgba(249,115,22,0.85)',
                    'rgba(168,85,247,0.85)',
                    'rgba(148,163,184,0.85)',
                    'rgba(239,68,68,0.85)',
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
})();
</script>
@endpush
@endsection