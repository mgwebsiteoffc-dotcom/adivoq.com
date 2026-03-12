@extends('layouts.tenant')

@section('title', 'Record Payment')
@section('page_title', 'Record Payment')

@section('content')
<div class="max-w-xl space-y-6">

    <a href="{{ route('tenant.payments.index') }}"
       class="text-surface-400 hover:text-white text-sm">
        ← Back to Payments
    </a>

    <form method="POST"
          action="{{ route('tenant.payments.store') }}"
          class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
        @csrf

        <div>
            <label class="block text-sm text-surface-400 mb-1">Invoice *</label>
            <select name="invoice_id" required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
                <option value="">Select Invoice</option>
                @foreach($invoices as $invoice)
                    <option value="{{ $invoice->id }}">
                        {{ $invoice->invoice_number }} (₹{{ number_format($invoice->balance_due,0) }} due)
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Amount *</label>
            <input type="number" name="amount" required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Payment Date *</label>
            <input type="date" name="payment_date" required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Method *</label>
            <select name="payment_method"
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
                <option value="bank_transfer">Bank Transfer</option>
                <option value="upi">UPI</option>
                <option value="cash">Cash</option>
                <option value="cheque">Cheque</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Transaction ID</label>
            <input type="text" name="transaction_id"
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
                Save Payment
            </button>

            <a href="{{ route('tenant.payments.index') }}"
               class="px-6 py-2 text-surface-400 hover:text-white text-sm">
                Cancel
            </a>
        </div>

    </form>

</div>
@endsection