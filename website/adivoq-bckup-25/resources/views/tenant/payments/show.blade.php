@extends('layouts.tenant')

@section('title', 'Payment Details')
@section('page_title', 'Payment Details')

@section('content')
<div class="max-w-xl space-y-6">

    <a href="{{ route('tenant.payments.index') }}"
       class="text-surface-400 hover:text-white text-sm">
        ← Back to Payments
    </a>

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-4">

        <div class="flex justify-between">
            <span class="text-surface-400 text-sm">Invoice</span>
            <span class="text-white">
                {{ $payment->invoice->invoice_number }}
            </span>
        </div>

        <div class="flex justify-between">
            <span class="text-surface-400 text-sm">Brand</span>
            <span class="text-white">
                {{ $payment->brand->name }}
            </span>
        </div>

        <div class="flex justify-between">
            <span class="text-surface-400 text-sm">Amount</span>
            <span class="text-white font-mono">
                ₹{{ number_format($payment->amount, 0) }}
            </span>
        </div>

        <div class="flex justify-between">
            <span class="text-surface-400 text-sm">Date</span>
            <span class="text-white">
                {{ $payment->payment_date }}
            </span>
        </div>

        <div class="flex justify-between">
            <span class="text-surface-400 text-sm">Method</span>
            <span class="text-white">
                {{ ucfirst($payment->payment_method) }}
            </span>
        </div>

    </div>

</div>
@endsection