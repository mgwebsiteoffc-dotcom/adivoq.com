@extends('layouts.public')
@section('title', 'Refund Policy — InvoiceHero')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-8">Refund Policy</h1>
        <p class="text-sm text-gray-500 mb-8">Last updated: {{ date('F d, Y') }}</p>

        <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700">
            <h2>14-Day Free Trial</h2>
            <p>All paid plans come with a 14-day free trial. No credit card is required to start. You can evaluate all features before committing to a paid plan.</p>

            <h2>Refund Eligibility</h2>
            <p>If you're not satisfied with InvoiceHero, you may request a full refund within 7 days of your first payment. After 7 days, we do not offer refunds for the current billing period.</p>

            <h2>How to Request a Refund</h2>
            <p>Email us at <a href="mailto:billing@invoicehero.com">billing@invoicehero.com</a> with your account email and reason for refund. Refunds are processed within 5-7 business days to the original payment method.</p>

            <h2>Cancellation</h2>
            <p>You may cancel your subscription at any time. Your plan benefits continue until the end of the current billing period. After cancellation, your account reverts to the free plan.</p>

            <h2>Contact</h2>
            <p>For billing questions, reach us at <a href="mailto:billing@invoicehero.com">billing@invoicehero.com</a>.</p>
        </div>
    </div>
</section>
@endsection