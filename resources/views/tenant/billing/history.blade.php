@extends('layouts.tenant')
@section('title', 'Billing History')
@section('page_title', 'Subscription Payment History')

@section('content')
{{-- Back to Billing --}}
<div class="mb-6">
    <a href="{{ route('dashboard.billing.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-1"></i>Back to Billing
    </a>
</div>

{{-- Payment History --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Payment ID</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Plan</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Amount</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs">{{ $payment->razorpay_payment_id }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $payment->payment_date->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-gray-700 capitalize">
                            {{ $payment->plan }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">
                            ₹{{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full
                                {{ $payment->isCaptured() ? 'bg-green-100 text-green-700' :
                                   ($payment->isFailed() ? 'bg-red-100 text-red-700' :
                                    'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($payment->isCaptured())
                                <a href="{{ route('dashboard.billing.receipt', $payment) }}"
                                   class="p-1.5 text-gray-400 hover:text-indigo-600 rounded transition"
                                   title="Download Receipt">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-receipt text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1">No payment history</h3>
                            <p class="text-sm text-gray-500">Subscription payments will appear here.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $payments->links() }}</div>@endif
</div>
@endsection