<!-- resources/views/public/terms-of-service.blade.php -->
@extends('layouts.public')

@section('title', 'Terms of Service — AdivoQ')
@section('meta_description', 'Terms of Service for AdivoQ - Rules and guidelines for using our platform.')

@section('content')
<div class="py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white mb-8">Terms of Service</h1>
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
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing or using AdivoQ ("the Platform"), you agree to be bound by these Terms of Service. If you disagree with any part of these terms, you may not access the Platform.</p>
            </section>

            <section>
                <h2>2. Description of Service</h2>
                <p>AdivoQ is a financial management platform designed for content creators. Our services include:</p>
                <ul>
                    <li>Brand and client management</li>
                    <li>Invoice generation and management</li>
                    <li>Payment tracking and collection</li>
                    <li>Tax calculations and reporting</li>
                    <li>Financial analytics and reports</li>
                </ul>
            </section>

            <section>
                <h2>3. Account Registration</h2>
                <p>To use AdivoQ, you must:</p>
                <ul>
                    <li>Be at least 18 years old</li>
                    <li>Provide accurate and complete registration information</li>
                    <li>Maintain the security of your password</li>
                    <li>Accept responsibility for all activities under your account</li>
                </ul>
            </section>

            <section>
                <h2>4. Acceptable Use</h2>
                <p>You agree NOT to:</p>
                <ul>
                    <li>Use the Platform for any illegal purpose</li>
                    <li>Generate fraudulent invoices or financial documents</li>
                    <li>Attempt to gain unauthorized access to other accounts</li>
                    <li>Interfere with or disrupt the Platform</li>
                    <li>Upload malicious code or content</li>
                    <li>Resell or redistribute the service without permission</li>
                </ul>
            </section>

            <section>
                <h2>5. Subscription & Payment</h2>
                <p>Some features require a paid subscription:</p>
                <ul>
                    <li>Subscriptions are billed monthly or annually as selected</li>
                    <li>Prices are subject to change with 30 days notice</li>
                    <li>Refunds are available within 7 days of purchase for annual plans</li>
                    <li>Downgrading may result in loss of features and data limits</li>
                </ul>
            </section>

            <section>
                <h2>6. Data Ownership</h2>
                <p>You retain ownership of all data you upload to AdivoQ. By using the Platform, you grant us a license to use this data solely to provide our services to you. We do not claim ownership of your invoices, client information, or financial records.</p>
            </section>

            <section>
                <h2>7. Invoice & Tax Disclaimer</h2>
                <p>While AdivoQ provides tools for invoice generation and tax calculations:</p>
                <ul>
                    <li>You are responsible for the accuracy of your invoices</li>
                    <li>Tax calculations are estimates and should be verified</li>
                    <li>We are not a tax advisory service</li>
                    <li>Consult a qualified CA for tax filing and compliance</li>
                </ul>
            </section>

            <section>
                <h2>8. Third-Party Payment Processors</h2>
                <p>Payment collection features use third-party services (Razorpay, Stripe). Your use of these services is subject to their terms. AdivoQ is not responsible for payment processing issues outside our control.</p>
            </section>

            <section>
                <h2>9. Limitation of Liability</h2>
                <p>AdivoQ shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from:</p>
                <ul>
                    <li>Use or inability to use the Platform</li>
                    <li>Unauthorized access to your data</li>
                    <li>Any third-party conduct or content</li>
                    <li>Lost profits, revenue, or business opportunities</li>
                </ul>
                <p>Our total liability shall not exceed the amount paid by you in the past 12 months.</p>
            </section>

            <section>
                <h2>10. Service Availability</h2>
                <p>We strive for 99.9% uptime but do not guarantee uninterrupted service. We may temporarily suspend service for maintenance, updates, or circumstances beyond our control.</p>
            </section>

            <section>
                <h2>11. Account Termination</h2>
                <p>We reserve the right to suspend or terminate accounts that:</p>
                <ul>
                    <li>Violate these terms</li>
                    <li>Engage in fraudulent activity</li>
                    <li>Fail to pay subscription fees</li>
                    <li>Remain inactive for extended periods (Free plan: 12 months)</li>
                </ul>
                <p>You may cancel your account at any time. Upon cancellation, you can export your data within 30 days.</p>
            </section>

            <section>
                <h2>12. Changes to Terms</h2>
                <p>We may modify these terms at any time. Significant changes will be notified via email. Continued use after changes constitutes acceptance of new terms.</p>
            </section>

            <section>
                <h2>13. Governing Law</h2>
                <p>These terms shall be governed by the laws of India. Any disputes shall be subject to the exclusive jurisdiction of courts in Mumbai, Maharashtra.</p>
            </section>

            <section>
                <h2>14. Contact</h2>
                <p>For questions about these Terms of Service:</p>
                <ul>
                    <li>Email: legal@adivoq.com</li>
                    <li>Contact Form: <a href="{{ route('contact') }}">adivoq.com/contact</a></li>
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection