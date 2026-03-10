@extends('layouts.tenant')
@section('title','Payment')
@section('page_title','Payment Details')

@section('content')
<a href="{{ route('dashboard.payments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
    <i class="fas fa-arrow-left mr-1"></i>Back
</a>

<div class="bg-white rounded-xl border p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500">Invoice</p>
            <a href="{{ route('dashboard.invoices.show', $payment->invoice) }}" class="text-lg font-bold text-indigo-600 hover:underline">
                {{ $payment->invoice->invoice_number }}
            </a>
            <p class="text-sm text-gray-600 mt-1">{{ $payment->invoice->brand->name ?? '—' }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Amount</p>
            <p class="text-2xl font-black text-green-700">₹{{ number_format($payment->amount,2) }}</p>
        </div>
    </div>

    <hr class="my-5">

    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><span class="text-gray-500">Payment Date:</span> <span class="font-semibold">{{ $payment->payment_date->format('M d, Y') }}</span></div>
        <div><span class="text-gray-500">Method:</span> <span class="font-semibold">{{ ucfirst(str_replace('_',' ',$payment->payment_method)) }}</span></div>
        <div><span class="text-gray-500">Status:</span> <span class="font-semibold">{{ ucfirst($payment->status) }}</span></div>
        <div><span class="text-gray-500">Reference:</span> <span class="font-semibold">{{ $payment->transaction_reference ?: '—' }}</span></div>
    </dl>

    @if($payment->receipt_path)
        <div class="mt-5">
            <a href="{{ asset('storage/'.$payment->receipt_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-lg text-sm font-semibold hover:bg-gray-200">
                <i class="fas fa-receipt mr-2"></i>View Receipt
            </a>
        </div>
    @endif
</div>
@endsection