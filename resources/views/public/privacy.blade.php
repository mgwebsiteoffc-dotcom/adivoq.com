@extends('layouts.public')
@section('title', 'Privacy Policy — InvoiceHero')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-8">Privacy Policy</h1>
        <p class="text-sm text-gray-500 mb-8">Last updated: {{ date('F d, Y') }}</p>

        <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700">
            <h2>1. Information We Collect</h2>
            <p>We collect information you provide directly: name, email address, business details, tax information (PAN, GSTIN), bank details, and invoice data. We also collect usage data automatically through cookies and analytics.</p>

            <h2>2. How We Use Your Information</h2>
            <p>We use your data to provide the invoicing service, process payments, send notifications (email and WhatsApp), generate reports, and improve our platform. We never sell your personal data to third parties.</p>

            <h2>3. Data Storage & Security</h2>
            <p>Your data is stored securely on encrypted servers. Each tenant's data is logically isolated. We use industry-standard security practices including HTTPS, encrypted passwords, and secure payment processing through certified gateways.</p>

            <h2>4. Third-Party Services</h2>
            <p>We use third-party services including Razorpay/Stripe (payment processing), Whatify (WhatsApp messaging), and email providers. These services have their own privacy policies.</p>

            <h2>5. Data Retention</h2>
            <p>We retain your data as long as your account is active. Upon account deletion, personal data is removed within 30 days, though anonymized transaction records may be retained for legal compliance.</p>

            <h2>6. Your Rights</h2>
            <p>You may request access to, correction of, or deletion of your personal data at any time. You can export all your data from the Settings page. Contact us at privacy@invoicehero.com for data-related requests.</p>

            <h2>7. Contact</h2>
            <p>For privacy concerns, contact us at <a href="mailto:privacy@invoicehero.com">privacy@invoicehero.com</a>.</p>
        </div>
    </div>
</section>
@endsection