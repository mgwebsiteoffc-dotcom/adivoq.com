@extends('layouts.admin')
@section('title', 'Analytics')
@section('page_title', 'Platform Analytics')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Tenant Growth --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Tenant Growth (Last 12 Months)</h3>
        <div class="h-64">
            <canvas id="tenantGrowthChart"></canvas>
        </div>
    </div>

    {{-- Invoice Volume --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Invoice Volume (Last 12 Months)</h3>
        <div class="h-64">
            <canvas id="invoiceVolumeChart"></canvas>
        </div>
    </div>
</div>

{{-- Revenue by Plan --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
    <h3 class="text-sm font-bold text-gray-900 mb-4">Revenue by Plan</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($revenueByPlan as $plan => $data)
            @php
                $colors = ['free' => 'gray', 'starter' => 'blue', 'professional' => 'indigo', 'enterprise' => 'purple'];
                $c = $colors[$plan] ?? 'gray';
            @endphp
            <div class="bg-{{ $c }}-50 rounded-xl p-5 border border-{{ $c }}-100">
                <h4 class="text-sm font-bold text-{{ $c }}-900 capitalize mb-3">{{ $plan }}</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Tenants</span><span class="font-bold">{{ number_format($data['tenants']) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Invoices</span><span class="font-bold">{{ number_format($data['invoices']) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Revenue</span><span class="font-bold text-green-600">₹{{ number_format($data['revenue']) }}</span></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Top Tenants --}}
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-bold text-gray-900 mb-4">Top Tenants by Revenue</h3>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-2 font-semibold text-gray-600">#</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-600">Tenant</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-600">Plan</th>
                <th class="text-right px-4 py-2 font-semibold text-gray-600">Revenue</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($topTenants as $i => $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-400 font-medium">{{ $i + 1 }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.tenants.show', $t) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $t->name }}</a>
                        <p class="text-xs text-gray-500">{{ $t->email }}</p>
                    </td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700 capitalize">{{ $t->plan }}</span></td>
                    <td class="px-4 py-2 text-right font-bold text-green-600">₹{{ number_format($t->total_revenue) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Tenant Growth
new Chart(document.getElementById('tenantGrowthChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: {!! json_encode(collect($tenantGrowth)->pluck('month')) !!},
        datasets: [
            { label: 'New Signups', data: {!! json_encode(collect($tenantGrowth)->pluck('count')) !!}, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.1)', fill: true, tension: 0.3 },
            { label: 'Total', data: {!! json_encode(collect($tenantGrowth)->pluck('cumulative')) !!}, borderColor: '#22c55e', borderDash: [5,5], tension: 0.3, fill: false },
        ]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
});

// Invoice Volume
new Chart(document.getElementById('invoiceVolumeChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($invoiceVolume)->pluck('month')) !!},
        datasets: [{
            label: 'Invoices',
            data: {!! json_encode(collect($invoiceVolume)->pluck('count')) !!},
            backgroundColor: 'rgba(99,102,241,0.8)',
            borderRadius: 4,
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
@endsection