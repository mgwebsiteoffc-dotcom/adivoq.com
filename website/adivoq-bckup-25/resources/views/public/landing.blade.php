<!-- resources/views/public/landing.blade.php -->
@extends('layouts.public')

@section('title', 'AdivoQ — Financial OS for Creators')
@section('meta_description', 'Track revenue, manage brand deals, automate payments, and handle taxes — all in one place built for Indian creators.')

@section('content')
<div class="min-h-screen">

    {{-- Hero Section --}}
    <section class="relative py-20 sm:py-32 overflow-hidden">
        {{-- Background gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-brand-900/20 via-surface-900 to-indigo-900/20"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-brand-500/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-500/10 border border-brand-500/20 rounded-full text-sm text-brand-400 mb-6">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Built for Indian Creators
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight">
                    Stop Juggling Spreadsheets.<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-indigo-400">Start Growing.</span>
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-surface-400 max-w-3xl mx-auto">
                    AdivoQ is the financial operating system built for creators. Track brand deals, send GST invoices, collect payments, and file taxes — all in one beautiful platform.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/25 text-sm">
                        Start Free — No Card Required
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-3.5 border border-surface-700 text-surface-300 font-medium rounded-xl hover:border-surface-600 hover:text-white transition-all text-sm">
                        See How It Works
                    </a>
                </div>

                {{-- Trust badges --}}
                <div class="mt-12 flex flex-wrap items-center justify-center gap-6 text-surface-500 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        GST Compliant
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Razorpay & Stripe
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        TDS Tracking
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Free Forever Plan
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Problem Statement --}}
    <section class="py-20 border-t border-surface-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-6">
                    Managing creator finances is a <span class="text-red-400">nightmare</span>
                </h2>
                <div class="grid sm:grid-cols-3 gap-6 text-left">
                    <div class="bg-surface-800/50 border border-surface-700 rounded-xl p-5">
                        <div class="text-3xl mb-3">📊</div>
                        <p class="text-surface-400 text-sm">Tracking payments across 10 different spreadsheets</p>
                    </div>
                    <div class="bg-surface-800/50 border border-surface-700 rounded-xl p-5">
                        <div class="text-3xl mb-3">😰</div>
                        <p class="text-surface-400 text-sm">Chasing brands for payments via WhatsApp & email</p>
                    </div>
                    <div class="bg-surface-800/50 border border-surface-700 rounded-xl p-5">
                        <div class="text-3xl mb-3">🧾</div>
                        <p class="text-surface-400 text-sm">Last-minute tax panic during ITR filing season</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Grid --}}
    <section id="features" class="py-20 border-t border-surface-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3">Features</p>
                <h2 class="text-3xl sm:text-4xl font-bold text-white">Everything you need in one place</h2>
                <p class="mt-4 text-surface-400 max-w-2xl mx-auto">Stop paying for 5 different tools. AdivoQ combines brand management, invoicing, payments, and tax compliance into one platform.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $features = [
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                        'title' => 'Brand CRM',
                        'desc' => 'Store brand contacts, track deal history, and never lose important details. See total revenue from each brand at a glance.',
                        'color' => 'brand'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'title' => 'GST Invoicing',
                        'desc' => 'Generate professional GST-compliant invoices in seconds. Auto-calculate CGST, SGST, IGST based on client location.',
                        'color' => 'emerald'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                        'title' => 'Payment Tracking',
                        'desc' => 'Record manual payments or collect online via Razorpay/Stripe. Automatic payment reminders via email & WhatsApp.',
                        'color' => 'blue'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                        'title' => 'Campaign Tracker',
                        'desc' => 'Track milestones, deliverables, and deadlines for each brand deal. Know exactly where your money is coming from.',
                        'color' => 'amber'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>',
                        'title' => 'Tax Ready Reports',
                        'desc' => 'TDS tracking, GST summaries, and tax-ready reports. Share directly with your CA during filing season.',
                        'color' => 'rose'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        'title' => 'Team Access',
                        'desc' => 'Invite your manager or CA with role-based permissions. Everyone sees only what they need.',
                        'color' => 'indigo'
                    ],
                ];
                @endphp

                @foreach($features as $feature)
                <div class="group bg-surface-800 border border-surface-700 rounded-xl p-6 hover:border-{{ $feature['color'] }}-500/50 transition-all duration-300">
                    <div class="w-12 h-12 bg-{{ $feature['color'] }}-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $feature['icon'] !!}
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-surface-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-20 border-t border-surface-800 bg-surface-800/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3">How It Works</p>
                <h2 class="text-3xl sm:text-4xl font-bold text-white">Get started in 3 minutes</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl font-bold text-white">1</div>
                    <h3 class="text-xl font-semibold text-white mb-3">Sign Up Free</h3>
                    <p class="text-surface-400">Create your account in 30 seconds. No credit card required. Free plan available forever.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl font-bold text-white">2</div>
                    <h3 class="text-xl font-semibold text-white mb-3">Add Your Brands</h3>
                    <p class="text-surface-400">Import your brand contacts and past deals. Set up your tax details (PAN, GSTIN).</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl font-bold text-white">3</div>
                    <h3 class="text-xl font-semibold text-white mb-3">Send Invoices & Get Paid</h3>
                    <p class="text-surface-400">Create professional invoices, share payment links, and track everything automatically.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="pricing" class="py-20 border-t border-surface-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3">Pricing</p>
                <h2 class="text-3xl sm:text-4xl font-bold text-white">Simple, transparent pricing</h2>
                <p class="mt-4 text-surface-400">Start free. Upgrade when you grow.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                {{-- Free --}}
                <div class="bg-surface-800 border border-surface-700 rounded-2xl p-8">
                    <h3 class="text-lg font-semibold text-white">Free</h3>
                    <p class="text-surface-400 text-sm mt-1">For creators just starting out</p>
                    <div class="mt-6">
                        <span class="text-4xl font-bold text-white">₹0</span>
                        <span class="text-surface-400">/month</span>
                    </div>
                    <ul class="mt-8 space-y-4">
                        @foreach(['5 Brands', '10 Invoices/month', 'GST Invoicing', 'Payment Tracking', 'Basic Reports'] as $item)
                        <li class="flex items-center gap-3 text-sm text-surface-300">
                            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 block w-full py-3 text-center bg-surface-700 text-white font-medium rounded-xl hover:bg-surface-600 transition-colors text-sm">
                        Get Started Free
                    </a>
                </div>

                {{-- Pro --}}
                <div class="bg-gradient-to-b from-brand-600/20 to-surface-800 border-2 border-brand-500 rounded-2xl p-8 relative">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-brand-600 text-white text-xs font-semibold rounded-full">
                        MOST POPULAR
                    </div>
                    <h3 class="text-lg font-semibold text-white">Pro</h3>
                    <p class="text-surface-400 text-sm mt-1">For growing creators</p>
                    <div class="mt-6">
                        <span class="text-4xl font-bold text-white">₹499</span>
                        <span class="text-surface-400">/month</span>
                    </div>
                    <ul class="mt-8 space-y-4">
                        @foreach(['50 Brands', '100 Invoices/month', 'Everything in Free', 'Payment Links', 'WhatsApp Reminders', 'Advanced Reports', '3 Team Members'] as $item)
                        <li class="flex items-center gap-3 text-sm text-surface-300">
                            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 block w-full py-3 text-center bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition-colors text-sm shadow-lg shadow-brand-500/25">
                        Start 14-Day Free Trial
                    </a>
                </div>

                {{-- Business --}}
                <div class="bg-surface-800 border border-surface-700 rounded-2xl p-8">
                    <h3 class="text-lg font-semibold text-white">Business</h3>
                    <p class="text-surface-400 text-sm mt-1">For agencies & teams</p>
                    <div class="mt-6">
                        <span class="text-4xl font-bold text-white">₹1,499</span>
                        <span class="text-surface-400">/month</span>
                    </div>
                    <ul class="mt-8 space-y-4">
                        @foreach(['Unlimited Brands', 'Unlimited Invoices', 'Everything in Pro', 'Unlimited Team', 'API Access', 'Priority Support', 'Custom Branding'] as $item)
                        <li class="flex items-center gap-3 text-sm text-surface-300">
                            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('contact') }}" class="mt-8 block w-full py-3 text-center bg-surface-700 text-white font-medium rounded-xl hover:bg-surface-600 transition-colors text-sm">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-20 border-t border-surface-800 bg-surface-800/30">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white">Frequently Asked Questions</h2>
            </div>

            <div class="space-y-4" x-data="{ open: null }">
                @php
                $faqs = [
                    ['q' => 'Is AdivoQ free to use?', 'a' => 'Yes! Our Free plan is free forever with 5 brands and 10 invoices per month. No credit card required to sign up.'],
                    ['q' => 'Do I need GST registration to use AdivoQ?', 'a' => 'No. You can use AdivoQ even without GST registration. If you\'re not registered, invoices will be generated without GST. Once you register, simply add your GSTIN in settings.'],
                    ['q' => 'How does the GST calculation work?', 'a' => 'AdivoQ automatically calculates GST based on your and your client\'s location. Same state = CGST + SGST. Different state = IGST. You can also override rates per invoice.'],
                    ['q' => 'Can brands pay me directly through AdivoQ?', 'a' => 'Yes! Connect your Razorpay or Stripe account and generate payment links. Brands can pay via UPI, cards, or netbanking. Money goes directly to your account.'],
                    ['q' => 'Is my data safe?', 'a' => 'Absolutely. We use bank-grade encryption (256-bit SSL), and your data is stored on secure servers. We never share your data with third parties.'],
                    ['q' => 'Can I invite my CA or manager?', 'a' => 'Yes! Pro and Business plans allow team members with role-based access. Your CA can view reports without seeing your client details.'],
                ];
                @endphp

                @foreach($faqs as $index => $faq)
                <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
                    <button
                        @click="open = open === {{ $index }} ? null : {{ $index }}"
                        class="w-full px-6 py-4 text-left flex items-center justify-between"
                    >
                        <span class="font-medium text-white">{{ $faq['q'] }}</span>
                        <svg class="w-5 h-5 text-surface-400 transition-transform" :class="open === {{ $index }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === {{ $index }}" x-collapse>
                        <div class="px-6 pb-4 text-surface-400 text-sm">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="py-20 border-t border-surface-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Ready to take control of your finances?</h2>
            <p class="text-surface-400 text-lg mb-8">Join hundreds of Indian creators who've already simplified their financial life.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/25">
                    Get Started Free →
                </a>
                <a href="{{ route('tools.tax-calculator') }}" class="w-full sm:w-auto px-8 py-3.5 border border-surface-700 text-surface-300 font-medium rounded-xl hover:border-surface-600 hover:text-white transition-all">
                    Try Tax Calculator
                </a>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
{{-- Alpine Collapse Plugin --}}
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
@endpush