@extends('layouts.tenant')
@section('title','Payments')
@section('page_title','Payments')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">This Month</p>
        <p class="text-2xl font-bold text-green-600 mt-1">₹{{ number_format($stats['this_month']) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">This Year</p>
        <p class="text-2xl font-bold text-indigo-600 mt-1">₹{{ number_format($stats['this_year']) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">Total Received</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">₹{{ number_format($stats['total']) }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border p-4 mb-6">
    <form class="flex flex-wrap gap-3 items-end" method="GET">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Method</label>
            <select name="method" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">All</option>
                @foreach(['bank_transfer','upi','cash','cheque','razorpay','stripe','other'] as $m)
                    <option value="{{ $m }}" @selected(request('method')===$m)>{{ ucfirst(str_replace('_',' ',$m)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">All</option>
                @foreach(['confirmed','pending','failed','refunded'] as $s)
                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border rounded-lg text-sm">
        </div>

        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">
            Filter
        </button>
        @if(request()->query())
            <a href="{{ route('dashboard.payments.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200">
                Clear
            </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Invoice</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Brand</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Method</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Amount</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($payments as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a class="font-semibold text-indigo-600 hover:underline" href="{{ route('dashboard.invoices.show', $p->invoice) }}">
                                {{ $p->invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $p->invoice->brand->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $p->payment_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-green-700">₹{{ number_format($p->amount,2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('dashboard.payments.show', $p) }}" class="text-xs font-semibold text-gray-700 hover:text-indigo-600">
                                View →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-14 text-center text-gray-500">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t">{{ $payments->links() }}</div>
</div>
@endsection