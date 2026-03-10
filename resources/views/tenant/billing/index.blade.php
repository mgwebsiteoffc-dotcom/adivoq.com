@extends('layouts.tenant')
@section('title','Billing')
@section('page_title','Billing & Plans')

@section('content')
<div class="bg-white rounded-xl border p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500">Current Plan</p>
            <p class="text-2xl font-black text-gray-900">{{ ucfirst($tenant->plan) }}</p>
            <p class="text-sm text-gray-600 mt-1">Status: <span class="font-bold">{{ ucfirst($tenant->plan_status) }}</span></p>
            @if($tenant->subscription_ends_at)
                <p class="text-xs text-gray-500 mt-1">Valid until: {{ $tenant->subscription_ends_at->format('M d, Y') }}</p>
            @endif
        </div>

        @if($subscription && in_array($subscription->status, ['active','authenticated']))
            <form method="POST" action="{{ route('dashboard.billing.cancel') }}" onsubmit="return confirm('Cancel at end of cycle?')">
                @csrf
                <button class="px-5 py-2.5 bg-red-100 text-red-700 rounded-lg text-sm font-black hover:bg-red-200">
                    Cancel Subscription
                </button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-5" id="plans">
@foreach($paidPlans as $key)
    @php $p = $plans[$key]; $popular = $key === 'professional'; @endphp
    <div class="bg-white rounded-2xl border {{ $popular ? 'border-indigo-300 shadow-lg' : 'border-gray-200' }} p-6">
        @if($popular)
            <div class="inline-block mb-3 px-3 py-1 text-xs font-black rounded-full bg-indigo-100 text-indigo-700">Most Popular</div>
        @endif

        <h3 class="text-lg font-black text-gray-900">{{ $p['name'] }}</h3>
        <p class="text-3xl font-black text-gray-900 mt-3">₹{{ number_format($p['price']) }} <span class="text-sm text-gray-500 font-semibold">/month</span></p>

        <ul class="mt-4 space-y-2 text-sm text-gray-700">
            <li>Invoices / month: <b>{{ $p['invoices_per_month'] === -1 ? 'Unlimited' : $p['invoices_per_month'] }}</b></li>
            <li>Brands: <b>{{ $p['brands'] === -1 ? 'Unlimited' : $p['brands'] }}</b></li>
            <li>Team: <b>{{ $p['team_members'] === -1 ? 'Unlimited' : $p['team_members'] }}</b></li>
        </ul>

        <button
            class="mt-6 w-full px-5 py-3 rounded-xl text-sm font-black {{ $tenant->plan === $key ? 'bg-gray-100 text-gray-500' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}"
            {{ $tenant->plan === $key ? 'disabled' : '' }}
            onclick="startCheckout('{{ $key }}')">
            {{ $tenant->plan === $key ? 'Current Plan' : 'Upgrade to ' . $p['name'] }}
        </button>
    </div>
@endforeach
</div>

<div id="billing_msg" class="mt-6 text-sm"></div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
async function startCheckout(planKey) {
    const msg = document.getElementById('billing_msg');
    msg.className = 'mt-6 text-sm text-gray-700';
    msg.innerText = 'Creating subscription...';

    const res = await fetch("{{ route('dashboard.billing.razorpay.create') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ plan: planKey })
    });

    const json = await res.json();
    if (!json.success) {
        msg.className = 'mt-6 text-sm text-red-600';
        msg.innerText = json.message || 'Failed to create subscription.';
        return;
    }

    const options = {
        key: json.key_id,
        subscription_id: json.subscription_id,
        name: "{{ $tenant->business_name ?? $tenant->name }}",
        description: "InvoiceHero Subscription",
        handler: async function (response) {
            msg.innerText = 'Verifying payment...';

            const v = await fetch("{{ route('dashboard.billing.razorpay.verify') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_subscription_id: response.razorpay_subscription_id,
                    razorpay_signature: response.razorpay_signature
                })
            });

            const vj = await v.json();
            if (vj.success) {
                window.location.href = vj.redirect;
            } else {
                window.location.href = json.redirect_failed + '&message=' + encodeURIComponent(vj.message || 'Verification failed');
            }
        },
        theme: { color: "#4F46E5" }
    };

    const rzp = new Razorpay(options);
    rzp.on('payment.failed', function(resp) {
        const reason = resp?.error?.description || 'Payment failed';
        window.location.href = json.redirect_failed + '&message=' + encodeURIComponent(reason);
    });

    rzp.open();
}
</script>
@endpush
@endsection