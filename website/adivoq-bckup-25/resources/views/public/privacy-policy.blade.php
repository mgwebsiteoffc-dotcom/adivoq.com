<!-- resources/views/public/privacy-policy.blade.php -->
@extends('layouts.public')

@section('title', 'Privacy Policy — AdivoQ')
@section('meta_description', 'Privacy Policy for AdivoQ - How we collect, use, and protect your data.')

@section('content')
<div class="py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white mb-8">Privacy Policy</h1>
        <p class="text-surface-400 mb-8">Last updated: {{ date('F d, Y') }}</p>

        <div class="prose prose-invert max-w-none space-y-8">
            <style>
                .prose h2 { color: #f8fafc; font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; }
                .prose p, .prose li { color: #94a3b8; line-height: 1.75; }
                .prose ul { padding-left: 1.5rem; list-style-type: disc; }
                .prose li { margin-bottom: 0.5rem; }
                .prose a { color: #a78bfa; }
            </style>

            <section>
                <h2>1. Introduction</h2>
                <p>AdivoQ ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform.</p>
            </section>

            <section>
                <h2>2. Information We Collect</h2>
                <p>We collect information you provide directly to us:</p>
                <ul>
                    <li><strong>Account Information:</strong> Name, email address, phone number, password</li>
                    <li><strong>Business Information:</strong> Business name, address, PAN, GSTIN, bank details</li>
                    <li><strong>Financial Data:</strong> Invoice details, payment records, brand information</li>
                    <li><strong>Usage Data:</strong> How you interact with our platform, features used, time spent</li>
                </ul>
            </section>

            <section>
                <h2>3. How We Use Your Information</h2>
                <p>We use the collected information to:</p>
                <ul>
                    <li>Provide, maintain, and improve our services</li>
                    <li>Process transactions and send related information</li>
                    <li>Send technical notices, updates, and support messages</li>
                    <li>Respond to your comments, questions, and requests</li>
                    <li>Generate invoices, reports, and tax documents</li>
                    <li>Detect, investigate, and prevent fraudulent activities</li>
                </ul>
            </section>

            <section>
                <h2>4. Data Storage & Security</h2>
                <p>Your data is stored on secure servers with industry-standard encryption (256-bit SSL). We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
            </section>

            <section>
                <h2>5. Third-Party Services</h2>
                <p>We may use third-party services for:</p>
                <ul>
                    <li><strong>Payment Processing:</strong> Razorpay, Stripe (they have their own privacy policies)</li>
                    <li><strong>Email Services:</strong> For sending invoices and notifications</li>
                    <li><strong>Analytics:</strong> To understand platform usage and improve services</li>
                </ul>
                <p>We do not sell your personal information to third parties.</p>
            </section>

            <section>
                <h2>6. Data Retention</h2>
                <p>We retain your personal information for as long as your account is active or as needed to provide services. Financial records are retained as required by Indian tax laws (typically 7-8 years). You can request data deletion by contacting us.</p>
            </section>

            <section>
                <h2>7. Your Rights</h2>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal data</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Export your data in a portable format</li>
                    <li>Withdraw consent for data processing</li>
                </ul>
            </section>

            <section>
                <h2>8. Cookies</h2>
                <p>We use cookies and similar technologies to maintain your session, remember preferences, and analyze platform usage. You can control cookies through your browser settings.</p>
            </section>

            <section>
                <h2>9. Children's Privacy</h2>
                <p>AdivoQ is not intended for users under 18 years of age. We do not knowingly collect personal information from children.</p>
            </section>

            <section>
                <h2>10. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of significant changes by email or through the platform. Continued use after changes constitutes acceptance.</p>
            </section>

            <section>
                <h2>11. Contact Us</h2>
                <p>If you have questions about this Privacy Policy, please contact us at:</p>
                <ul>
                    <li>Email: privacy@adivoq.com</li>
                    <li>Contact Form: <a href="{{ route('contact') }}">adivoq.com/contact</a></li>
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection