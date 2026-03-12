@extends('layouts.tenant')

@section('title', 'Invoices')
@section('page_title', 'Invoices')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-white text-lg font-semibold">All Invoices</h2>

        <a href="{{ route('tenant.invoices.create') }}"
           class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
            + Create Invoice
        </a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-surface-700/50 text-surface-400 text-xs uppercase">
                <tr>
                    <th class="px-6 py-4 text-left">Invoice</th>
                    <th class="px-6 py-4">Brand</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-700">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-surface-700/30">
                    <td class="px-6 py-4 text-white">
                        {{ $invoice->invoice_number }}
                    </td>
                    <td class="px-6 py-4 text-surface-400">
                        {{ $invoice->brand->name }}
                    </td>
                    <td class="px-6 py-4 text-surface-400 font-mono">
                        ₹{{ number_format($invoice->total_amount, 0) }}
                    </td>
                    <td class="px-6 py-4 text-surface-400">
                        {{ ucfirst($invoice->status) }}
                    </td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="{{ route('tenant.invoices.show', $invoice) }}"
                           class="text-brand-400 text-sm">
                           View
                        </a>

                        <form action="{{ route('tenant.invoices.destroy', $invoice) }}"
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-400 text-sm"
                                onclick="return confirm('Delete invoice?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-surface-500">
                        No invoices yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $invoices->links() }}

</div>
@endsection