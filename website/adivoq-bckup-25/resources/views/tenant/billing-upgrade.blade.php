@extends('layouts.tenant')

@section('title','Upgrade to Pro')
@section('page_title','Upgrade to Pro')

@section('content')
<div class="glass rounded-2xl p-6 max-w-lg mx-auto space-y-8">

    <h3 class="text-lg font-semibold text-white mb-6">Upgrade to Pro</h3>
    <p class="text-surface-400 mb-4">Subscribe to Pro for unlimited invoices, advanced reports, and more.</p>

    <form id="razorpay-subscribe-form">
        <button type="button" id="rzp-button" class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
            Pay ₹499/month with Razorpay
        </button>
    </form>

</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('rzp-button').onclick = function(e){
    e.preventDefault();

    var options = {
        "key": "{{ env('RAZORPAY_KEY_ID') }}",
        "subscription_id": "", // Will be set after AJAX
        "name": "AdivoQ Pro Subscription",
        "description": "Monthly Subscription",
        "handler": function (response){
            window.location.href = "/dashboard/billing/success?razorpay_payment_id=" + response.razorpay_payment_id;
        },
        "theme": {
            "color": "#8b5cf6"
        }
    };

    // Create subscription via AJAX
    fetch('/dashboard/billing/create-subscription', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            plan_id: "{{ $razorpayPlanId }}"
        })
    })
    .then(response => response.json())
    .then(data => {
        options.subscription_id = data.subscription_id;
        var rzp = new Razorpay(options);
        rzp.open();
    });
};
</script>
@endsection