@extends('layouts.tenant')
@section('title', 'Recurring Invoices')
@section('page_title', 'Recurring Invoice Management')

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @php
        $total = $recurringInvoices->total();
        $active = $recurringInvoices->where('paused', false)->count();
        $paused = $recurringInvoices->where('paused', true)->count();
    @endphp
    <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <p class="text-lg font-bold text-gray-600">{{ $total }}</p>
        <p class="text-xs text-gray-500">Total Templates</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <p class="text-lg font-bold text-green-600">{{ $active }}</p>
        <p class="text-xs text-gray-500">Active</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <p class="text-lg font-bold text-yellow-600">{{ $paused }}</p>
        <p class="text-xs text-gray-500">Paused</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
        <p class="text-lg font-bold text-blue-600">{{ $recurringInvoices->where('next_recurring_date', '<=', now())->where('paused', false)->count() }}</p>
        <p class="text-xs text-gray-500">Due Today</p>
    </div>
</div>

{{-- Back to Invoices --}}
<div class="flex justify-between items-center mb-4">
    <a href="{{ route('dashboard.invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-1"></i>Back to Invoices
    </a>
    <a href="{{ route('dashboard.invoices.create') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 shadow-sm">
        <i class="fas fa-plus mr-1.5"></i>Create Invoice
    </a>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Template</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Brand</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Frequency</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Next Invoice</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Amount</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recurringInvoices as $invoice)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <a href="{{ route('dashboard.invoices.show', $invoice) }}" class="font-semibold text-gray-900 hover:text-indigo-600 transition">
                                {{ $invoice->invoice_number }}
                            </a>
                            @if($invoice->reference_number)
                                <br><span class="text-xs text-gray-500">{{ $invoice->reference_number }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $invoice->brand->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700 capitalize">{{ $invoice->recurring_frequency }}</td>
                        <td class="px-4 py-3 text-xs {{ $invoice->next_recurring_date <= now() && !$invoice->paused ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                            {{ $invoice->next_recurring_date->format('M d, Y') }}
                            @if($invoice->next_recurring_date <= now() && !$invoice->paused)
                                <br><span class="text-red-500">Due now</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">₹{{ number_format($invoice->total_amount) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($invoice->paused)
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-pause mr-1"></i>Paused
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    <i class="fas fa-play mr-1"></i>Active
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('dashboard.invoices.show', $invoice) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 rounded transition" title="View"><i class="fas fa-eye text-xs"></i></a>
                                <a href="{{ route('dashboard.invoices.recurring.edit', $invoice) }}" class="p-1.5 text-gray-400 hover:text-blue-600 rounded transition" title="Edit Recurring"><i class="fas fa-edit text-xs"></i></a>
                                @if($invoice->paused)
                                    <form method="POST" action="{{ route('dashboard.invoices.recurring.resume', $invoice) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-green-600 rounded transition" title="Resume">
                                            <i class="fas fa-play text-xs"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dashboard.invoices.recurring.pause', $invoice) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-yellow-600 rounded transition" title="Pause">
                                            <i class="fas fa-pause text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-sync text-indigo-400 text-xl"></i>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1">No recurring invoices yet</h3>
                            <p class="text-sm text-gray-500 mb-4">Create your first recurring invoice template to automate billing.</p>
                            <a href="{{ route('dashboard.invoices.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-plus mr-1.5"></i>Create Recurring Invoice
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($recurringInvoices->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $recurringInvoices->links() }}</div>@endif
</div>
@endsection