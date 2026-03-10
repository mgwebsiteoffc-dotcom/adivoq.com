@extends('layouts.public')
@section('title', 'Terms of Service — InvoiceHero')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-8">Terms of Service</h1>
        <p class="text-sm text-gray-500 mb-8">Last updated: {{ date('F d, Y') }}</p>

        <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing or using InvoiceHero, you agree to be bound by these Terms of Service. If you do not agree, please do not use our service.</p>

            <h2>2. Service Description</h2>
            <p>InvoiceHero is a SaaS invoicing platform for content creators. We provide tools to create invoices, manage brands, track payments, and generate financial reports.</p>

            <h2>3. Account Responsibilities</h2>
            <p>You are responsible for maintaining the security of your account credentials. You must provide accurate business information, particularly for GST and tax-related features. You are responsible for all activity under your account.</p>

            <h2>4. Acceptable Use</h2>
            <p>You may not use InvoiceHero for fraudulent invoicing, money laundering, or any illegal activity. You must comply with all applicable tax laws in your jurisdiction.</p>

            <h2>5. Billing & Payments</h2>
            <p>Paid plans are billed monthly or annually. You may upgrade, downgrade, or cancel at any time. Cancellation takes effect at the end of the current billing period. We do not provide prorated refunds for partial months.</p>

            <h2>6. Data Ownership</h2>
            <p>You retain full ownership of all data you create in InvoiceHero, including invoices, brand information, and financial records. You may export your data at any time.</p>

            <h2>7. Limitation of Liability</h2>
            <p>InvoiceHero is a tool to assist with invoicing and is not a substitute for professional accounting or legal advice. We are not liable for tax calculation errors — please verify all tax amounts with your chartered accountant.</p>

            <h2>8. Termination</h2>
            <p>We reserve the right to suspend or terminate accounts that violate these terms. You may delete your account at any time through Settings.</p>

            <h2>9. Changes</h2>
            <p>We may update these terms from time to time. Continued use constitutes acceptance of any changes.</p>

            <h2>10. Contact</h2>
            <p>Questions about these terms? Contact us at <a href="mailto:legal@invoicehero.com">legal@invoicehero.com</a>.</p>
        </div>
    </div>
</section>
@endsection