@extends('layouts.tenant')
@section('title','Invoice Aging')
@section('page_title','Invoice Aging')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full lg:w-auto">
        @foreach($buckets as $k => $b)
            <div class="bg-white rounded-xl border p-4">
                <p class="text-xs text-gray-500 font-semibold uppercase">{{ $b['label'] }}</p>
                <p class="text-xl font-black text-red-700 mt-1">₹{{ number_format($b['sum'],2) }}</p>
            </div>
        @endforeach
    </div>

    <a href="{{ route('dashboard.reports.export', ['type' => 'invoices']) }}"
       class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-black hover:bg-green-700">
        <i class="fas fa-download mr-2"></i>Export Invoices CSV
    </a>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Invoice</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Brand</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Due Date</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Due Amount</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoices as $inv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a class="font-black text-indigo-600 hover:underline" href="{{ route('dashboard.invoices.show', $inv) }}">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $inv->brand->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs {{ $inv->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                            {{ $inv->due_date->format('M d, Y') }}
                            @if($inv->isOverdue())
                                <span class="block text-red-500">{{ now()->diffInDays($inv->due_date) }} days overdue</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-black text-red-700">₹{{ number_format($inv->amount_due,2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs font-black rounded-full {{ $inv->isOverdue() ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $inv->isOverdue() ? 'Overdue' : ucfirst(str_replace('_',' ',$inv->status)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-14 text-center text-gray-500">No outstanding invoices.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection