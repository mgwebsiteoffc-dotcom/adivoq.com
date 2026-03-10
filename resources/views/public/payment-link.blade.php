@extends('layouts.public')
@section('title', 'Invoice #' . $invoice->invoice_number . ' — Payment')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Invoice Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">
            {{-- Header --}}
            <div class="gradient-bg p-6 lg:p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white/70">Invoice from</p>
                        <h2 class="text-xl font-bold mt-1">{{ $tenant->business_name ?? $tenant->name }}</h2>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-white/70">Invoice</p>
                        <p class="text-lg font-bold">#{{ $invoice->invoice_number }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 lg:p-8">
                {{-- Amount Due --}}
                <div class="text-center py-8 border-b border-gray-100 mb-8">
                    <p class="text-sm text-gray-500 font-medium">Amount Due</p>
                    <p class="text-5xl font-black text-gray-900 mt-2">{{ $invoice->currency_symbol }}{{ number_format($invoice->amount_due, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-2">Due on {{ $invoice->due_date->format('d M Y') }}</p>

                    @if($invoice->isOverdue())
                        <span class="inline-block mt-3 px-4 py-1.5 bg-red-100 text-red-700 text-sm font-semibold rounded-full">
                            <i class="fas fa-exclamation-circle mr-1"></i>Overdue by {{ now()->diffInDays($invoice->due_date) }} days
                        </span>
                    @endif
                </div>

                @if(($gatewaySetting?->razorpay_enabled ?? false) && !empty($razorpayKeyId) && $invoice->amount_due > 0)
    <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-8">
        <h3 class="text-sm font-bold text-gray-900 mb-2">
            <i class="fas fa-bolt text-indigo-600 mr-2"></i>Pay Online (Razorpay)
        </h3>
        <p class="text-xs text-gray-500 mb-4">Pay securely using UPI / Card / NetBanking. Payment updates automatically.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Amount (₹)</label>
                <input id="rp_amount" type="number" step="0.01" min="1" max="{{ $invoice->amount_due }}"
                       value="{{ $invoice->amount_due }}"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-400 mt-1">Max: ₹{{ number_format($invoice->amount_due,2) }}</p>
            </div>

            <div class="flex items-end">
                <button type="button" id="rp_pay_btn"
                        class="w-full px-5 py-3 gradient-bg text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                    Pay Now
                </button>
            </div>
        </div>

        <div id="rp_msg" class="mt-4 text-sm"></div>
    </div>

    @push('scripts')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            (function () {
                const payBtn = document.getElementById('rp_pay_btn');
                const msg = document.getElementById('rp_msg');
                const amountInput = document.getElementById('rp_amount');

                function setMsg(text, type='info') {
                    msg.className = 'mt-4 text-sm ' + (type === 'error' ? 'text-red-600' : (type === 'success' ? 'text-green-700' : 'text-gray-700'));
                    msg.innerText = text;
                }

                payBtn.addEventListener('click', async () => {
                    try {
                        setMsg('Creating payment order...', 'info');

                        const amount = parseFloat(amountInput.value || '0');
                        if (!amount || amount < 1) {
                            setMsg('Enter a valid amount.', 'error');
                            return;
                        }

                        const orderRes = await fetch("{{ route('payment.link.razorpay.order', $invoice->payment_link_token) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ amount })
                        });

                        const orderJson = await orderRes.json();
                        if (!orderJson.success) {
                            setMsg(orderJson.message || 'Failed to create order.', 'error');
                            return;
                        }

                        setMsg('Opening Razorpay...', 'info');

                        const options = {
                            key: orderJson.key_id,
                            amount: orderJson.amount,
                            currency: orderJson.currency,
                            name: "{{ $tenant->business_name ?? $tenant->name }}",
                            description: "Invoice {{ $invoice->invoice_number }}",
                            order_id: orderJson.order_id,
                            handler: async function (response) {
                                setMsg('Verifying payment...', 'info');

                                const verifyRes = await fetch("{{ route('payment.link.razorpay.verify', $invoice->payment_link_token) }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        amount,
                                        razorpay_payment_id: response.razorpay_payment_id,
                                        razorpay_order_id: response.razorpay_order_id,
                                        razorpay_signature: response.razorpay_signature
                                    })
                                });

                                const verifyJson = await verifyRes.json();

                          if (verifyJson.success) {
  window.location.href = verifyJson.redirect || orderJson.redirect_success;
} else {
  window.location.href = orderJson.redirect_failed + '&message=' + encodeURIComponent(verifyJson.message || 'Payment verification failed');
}
                            },
                            theme: { color: "#4F46E5" }
                        };

                        const rzp = new Razorpay(options);
rzp.on('payment.failed', function (resp) {
    const reason = resp?.error?.description || 'Payment failed';
    window.location.href = orderJson.redirect_failed + '&message=' + encodeURIComponent(reason);
});
                        rzp.open();
                    } catch (e) {
                        setMsg(e.message || 'Something went wrong.', 'error');
                    }
                });
            })();
        </script>
    @endpush
@endif

                {{-- Items --}}
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Invoice Items</h3>
                    <div class="space-y-3">
                        @foreach($invoice->items as $item)
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $item->description }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->quantity }} × {{ $invoice->currency_symbol }}{{ number_format($item->unit_price, 2) }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">{{ $invoice->currency_symbol }}{{ number_format($item->amount, 2) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span>{{ $invoice->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</span></div>
                        @if($invoice->discount_amount > 0)
                            <div class="flex justify-between text-green-600"><span>Discount</span><span>-{{ $invoice->currency_symbol }}{{ number_format($invoice->discount_amount, 2) }}</span></div>
                        @endif
                        @if($invoice->total_tax > 0)
                            <div class="flex justify-between"><span class="text-gray-600">Tax</span><span>{{ $invoice->currency_symbol }}{{ number_format($invoice->total_tax, 2) }}</span></div>
                        @endif
                        <div class="flex justify-between text-lg font-bold pt-2 border-t"><span>Total</span><span>{{ $invoice->currency_symbol }}{{ number_format($invoice->total_amount, 2) }}</span></div>
                        @if($invoice->amount_paid > 0)
                            <div class="flex justify-between text-green-600"><span>Paid</span><span>-{{ $invoice->currency_symbol }}{{ number_format($invoice->amount_paid, 2) }}</span></div>
                            <div class="flex justify-between text-lg font-bold"><span>Due</span><span class="text-red-600">{{ $invoice->currency_symbol }}{{ number_format($invoice->amount_due, 2) }}</span></div>
                        @endif
                    </div>
                </div>

                {{-- Bank Details --}}
                @if($bankDetails)
                    <div class="bg-gray-50 rounded-xl p-5 mb-8">
                        <h3 class="text-sm font-bold text-gray-900 mb-3"><i class="fas fa-university mr-2 text-brand-600"></i>Bank Details for Payment</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                            <div><span class="text-gray-500">Bank:</span> <span class="font-medium">{{ $bankDetails->bank_name }}</span></div>
                            <div><span class="text-gray-500">Account:</span> <span class="font-medium">{{ $bankDetails->account_number }}</span></div>
                            <div><span class="text-gray-500">IFSC:</span> <span class="font-medium">{{ $bankDetails->ifsc_code }}</span></div>
                            <div><span class="text-gray-500">Name:</span> <span class="font-medium">{{ $bankDetails->account_holder_name }}</span></div>
                            @if($bankDetails->upi_id)
                                <div class="sm:col-span-2"><span class="text-gray-500">UPI:</span> <span class="font-medium">{{ $bankDetails->upi_id }}</span></div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Record Payment Form --}}
                <div class="bg-brand-50 rounded-xl p-6 border border-brand-100">
                    <h3 class="text-sm font-bold text-gray-900 mb-4"><i class="fas fa-check-circle mr-2 text-brand-600"></i>Already Paid? Let us know</h3>
                    <form method="POST" action="{{ route('payment.link.process', $invoice->payment_link_token) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Amount ({{ $invoice->currency_symbol }})</label>
                                <input type="number" name="amount" value="{{ $invoice->amount_due }}" step="0.01" min="1" max="{{ $invoice->amount_due }}" required
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Payment Method</label>
                                <select name="payment_method" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                                    <option value="bank_transfer">Bank Transfer (NEFT/RTGS)</option>
                                    <option value="upi">UPI</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Transaction Reference / UTR Number *</label>
                            <input type="text" name="transaction_reference" required placeholder="Enter UTR or transaction ID"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        </div>
                        <button type="submit" class="w-full py-3 gradient-bg text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Payment Confirmation
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">Powered by <a href="{{ route('home') }}" class="text-brand-500 hover:underline">InvoiceHero</a> — Invoicing for Creators</p>
        </div>
    </div>
</section>
@endsection