@extends('layouts.tenant')

@section('title', 'Payments')
@section('page_title', 'Payments')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-white text-lg font-semibold">All Payments</h2>

        <a href="{{ route('tenant.payments.create') }}"
           class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
            + Record Payment
        </a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-surface-700/50 text-surface-400 text-xs uppercase">
                <tr>
                    <th class="px-6 py-4 text-left">Invoice</th>
                    <th class="px-6 py-4">Brand</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Method</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-700">
                @forelse($payments as $payment)
                <tr class="hover:bg-surface-700/30">
                    <td class="px-6 py-4 text-white">
                        {{ $payment->invoice->invoice_number }}
                    </td>
                    <td class="px-6 py-4 text-surface-400">
                        {{ $payment->brand->name }}
                    </td>
                    <td class="px-6 py-4 text-surface-400 font-mono">
                        ₹{{ number_format($payment->amount, 0) }}
                    </td>
                    <td class="px-6 py-4 text-surface-400">
                        {{ $payment->payment_date }}
                    </td>
                    <td class="px-6 py-4 text-surface-400">
                        {{ ucfirst($payment->payment_method) }}
                    </td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="{{ route('tenant.payments.show', $payment) }}"
                           class="text-brand-400 text-sm">
                           View
                        </a>

                        <form action="{{ route('tenant.payments.destroy', $payment) }}"
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-400 text-sm"
                                onclick="return confirm('Delete payment?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                        No payments recorded.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $payments->links() }}

</div>
@endsection