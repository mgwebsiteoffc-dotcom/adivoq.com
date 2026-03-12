@extends('layouts.public')
@section('title', 'AdivoQ — Professional Invoicing for Content Creators')

@section('content')

{{-- ====================== HERO SECTION ====================== --}}
<section class="relative overflow-hidden hero-pattern">
    {{-- Decorative Elements --}}
    <div class="absolute top-20 left-10 w-72 h-72 bg-brand-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float"></div>
    <div class="absolute top-40 right-10 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 2s;"></div>
    <div class="absolute bottom-20 left-1/3 w-72 h-72 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 4s;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 lg:pt-28 lg:pb-32">
        <div class="text-center max-w-4xl mx-auto">
            {{-- Badge --}}
            <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-sm font-medium mb-8 animate-fade-in">
                <i class="fas fa-sparkles mr-2 text-brand-500"></i>
                Trusted by 2,000+ Indian creators
            </div>

            {{-- Headline --}}
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black text-gray-900 tracking-tight leading-[1.1] animate-slide-up">
                Invoicing that
                <span class="gradient-text"> actually works</span>
                <br>for creators
            </h1>

            <p class="mt-6 lg:mt-8 text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed animate-slide-up" style="animation-delay: 0.2s;">
                Stop struggling with spreadsheets. Create GST-compliant invoices, track payments, send reminders via WhatsApp — all in one beautiful platform.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-10 animate-slide-up" style="animation-delay: 0.4s;">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 gradient-bg text-white text-lg font-bold rounded-2xl shadow-2xl shadow-brand-500/30 hover:shadow-brand-500/50 hover:scale-105 transition-all duration-300 flex items-center justify-center">
                    Start Free — No Card Required
                    <i class="fas fa-arrow-right ml-3"></i>
                </a>
                <a href="{{ route('tools.invoice-generator') }}" class="w-full sm:w-auto px-8 py-4 bg-white text-gray-700 text-lg font-semibold rounded-2xl border-2 border-gray-200 hover:border-brand-300 hover:text-brand-600 transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-file-invoice mr-2 text-brand-500"></i>
                    Try Free Invoice Generator
                </a>
            </div>

            {{-- Trust Indicators --}}
            <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-3 mt-12 text-sm text-gray-500 animate-fade-in" style="animation-delay: 0.6s;">
                <span class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i>Free forever plan</span>
                <span class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i>GST & TDS compliant</span>
                <span class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i>WhatsApp reminders</span>
                <span class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i>No credit card</span>
            </div>
        </div>

        {{-- Hero Image / Dashboard Preview --}}
        <div class="mt-16 lg:mt-20 relative animate-slide-up" style="animation-delay: 0.8s;">
            <div class="relative max-w-5xl mx-auto">
                <div class="absolute -inset-4 gradient-bg rounded-3xl blur-2xl opacity-20"></div>
                <div class="relative bg-gray-900 rounded-2xl lg:rounded-3xl shadow-2xl overflow-hidden border border-gray-800">
                    {{-- Browser Toolbar --}}
                    <div class="flex items-center px-4 py-3 bg-gray-800 border-b border-gray-700">
                        <div class="flex space-x-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="flex-1 flex justify-center">
                            <div class="px-4 py-1 bg-gray-700 rounded-lg text-xs text-gray-400 font-mono">app.invoicehero.com/dashboard</div>
                        </div>
                    </div>
                    {{-- Dashboard Preview --}}
                    <div class="p-6 lg:p-8 bg-gray-50">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                                <p class="text-xs text-gray-500 font-medium">Revenue This Month</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">₹2,45,000</p>
                                <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up mr-1"></i>+23.5%</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                                <p class="text-xs text-gray-500 font-medium">Outstanding</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">₹85,000</p>
                                <p class="text-xs text-orange-600 mt-1">3 invoices pending</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm hidden sm:block">
                                <p class="text-xs text-gray-500 font-medium">Active Campaigns</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">12</p>
                                <p class="text-xs text-brand-600 mt-1">Across 8 brands</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm hidden sm:block">
                                <p class="text-xs text-gray-500 font-medium">Invoices Sent</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">156</p>
                                <p class="text-xs text-green-600 mt-1">94% collection rate</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-sm text-gray-800">Recent Invoices</h3>
                                <span class="text-xs text-brand-600 font-medium">View All →</span>
                            </div>
                            <div class="space-y-3">
                                @foreach([['brand' => 'Mamaearth', 'num' => 'INV-00142', 'amount' => '₹75,000', 'status' => 'Paid', 'color' => 'green'], ['brand' => 'boAt Lifestyle', 'num' => 'INV-00143', 'amount' => '₹45,000', 'status' => 'Sent', 'color' => 'blue'], ['brand' => 'Nykaa', 'num' => 'INV-00144', 'amount' => '₹1,20,000', 'status' => 'Draft', 'color' => 'gray']] as $inv)
                                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-brand-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-building text-brand-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $inv['brand'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $inv['num'] }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-gray-900">{{ $inv['amount'] }}</p>
                                            <span class="inline-block text-xs px-2 py-0.5 rounded-full bg-{{ $inv['color'] }}-100 text-{{ $inv['color'] }}-700">{{ $inv['status'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ====================== FEATURES SECTION ====================== --}}
<section class="py-20 lg:py-28 bg-gray-50" id="features">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-20">
            <span class="inline-block px-4 py-1.5 bg-brand-50 text-brand-700 text-sm font-semibold rounded-full border border-brand-200 mb-4">FEATURES</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">
                Everything creators need to
                <span class="gradient-text">get paid faster</span>
            </h2>
            <p class="mt-5 text-lg text-gray-600">From creating professional invoices to automated payment reminders — we handle the boring stuff so you can focus on creating.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @php
                $features = [
                    ['icon' => 'fa-file-invoice-dollar', 'color' => 'brand', 'title' => 'GST-Compliant Invoices', 'desc' => 'Auto-calculate CGST, SGST, or IGST based on state codes. Add TDS deductions. 100% compliant with Indian tax laws.'],
                    ['icon' => 'fa-brands fa-whatsapp', 'color' => 'green', 'title' => 'WhatsApp Reminders', 'desc' => 'Send invoices and payment reminders directly on WhatsApp. Brands respond faster when the reminder is in their chat.'],
                    ['icon' => 'fa-link', 'color' => 'purple', 'title' => 'Payment Links', 'desc' => 'Generate shareable payment links. Brands can pay via Razorpay, Stripe, or record bank transfers online.'],
                    ['icon' => 'fa-chart-pie', 'color' => 'orange', 'title' => 'Revenue Analytics', 'desc' => 'Track revenue by brand, platform, and campaign. Know exactly how much each brand owes you at a glance.'],
                    ['icon' => 'fa-building', 'color' => 'blue', 'title' => 'Brand & Campaign CRM', 'desc' => 'Manage all your brands in one place. Track campaigns, milestones, and link invoices to specific deals.'],
                    ['icon' => 'fa-shield-halved', 'color' => 'teal', 'title' => 'Tax Management', 'desc' => 'Track GST collected, TDS deducted, and estimate tax liability. Download tax-ready reports for your CA.'],
                ];
            @endphp

            @foreach($features as $f)
                @php
                    $colors = [
                        'brand' => ['bg' => 'bg-brand-100', 'text' => 'text-brand-600', 'border' => 'hover:border-brand-200'],
                        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'border' => 'hover:border-green-200'],
                        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'border' => 'hover:border-purple-200'],
                        'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'border' => 'hover:border-orange-200'],
                        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'border' => 'hover:border-blue-200'],
                        'teal' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'border' => 'hover:border-teal-200'],
                    ];
                    $c = $colors[$f['color']];
                @endphp
                <div class="group bg-white rounded-2xl p-7 lg:p-8 border border-gray-100 {{ $c['border'] }} transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="w-14 h-14 {{ $c['bg'] }} rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas {{ $f['icon'] }} {{ $c['text'] }} text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $f['title'] }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ====================== HOW IT WORKS ====================== --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block px-4 py-1.5 bg-green-50 text-green-700 text-sm font-semibold rounded-full border border-green-200 mb-4">HOW IT WORKS</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">
                Get paid in <span class="gradient-text">3 simple steps</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 relative">
            {{-- Connecting line --}}
            <div class="hidden md:block absolute top-16 left-1/6 right-1/6 h-0.5 bg-gradient-to-r from-brand-200 via-brand-400 to-brand-200"></div>

            @foreach([
                ['num' => '01', 'title' => 'Add Your Brand', 'desc' => 'Enter the brand details once — name, GSTIN, state code. Their info auto-fills on every invoice.', 'icon' => 'fa-building'],
                ['num' => '02', 'title' => 'Create Invoice', 'desc' => 'Pick a brand, add line items, and GST calculates automatically. Preview the professional PDF.', 'icon' => 'fa-file-invoice'],
                ['num' => '03', 'title' => 'Get Paid', 'desc' => 'Send via email or WhatsApp with a payment link. Track when they view it and get notified on payment.', 'icon' => 'fa-indian-rupee-sign'],
            ] as $step)
                <div class="relative text-center">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mx-auto shadow-xl shadow-brand-500/20 relative z-10">
                        <i class="fas {{ $step['icon'] }} text-white text-xl"></i>
                    </div>
                    <div class="mt-6">
                        <span class="text-sm font-bold text-brand-500 tracking-wider">STEP {{ $step['num'] }}</span>
                        <h3 class="text-xl font-bold text-gray-900 mt-2 mb-3">{{ $step['title'] }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ====================== SOCIAL PROOF / STATS ====================== --}}
<section class="py-16 gradient-bg relative overflow-hidden">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
            @foreach([
                ['value' => number_format(max($stats['creators'] ?? 0, 2000)) . '+', 'label' => 'Active Creators'],
                ['value' => number_format(max($stats['invoices'] ?? 0, 50000)) . '+', 'label' => 'Invoices Generated'],
                ['value' => '₹' . number_format(max(($stats['revenue'] ?? 0) / 10000000, 25), 1) . ' Cr+', 'label' => 'Revenue Tracked'],
                ['value' => '4.9/5', 'label' => 'Creator Rating'],
            ] as $stat)
                <div>
                    <p class="text-3xl sm:text-4xl lg:text-5xl font-black">{{ $stat['value'] }}</p>
                    <p class="text-sm text-white/70 mt-2 font-medium">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ====================== TESTIMONIALS ====================== --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1.5 bg-yellow-50 text-yellow-700 text-sm font-semibold rounded-full border border-yellow-200 mb-4">LOVED BY CREATORS</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">
                Creators <span class="gradient-text">love</span> InvoiceHero
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            @foreach([
                ['name' => 'Priya Sharma', 'handle' => '@priyacreates', 'platform' => 'YouTube • 500K subs', 'text' => 'Before InvoiceHero, I was using Google Docs to make invoices. Now everything is automated — GST calculation, payment reminders, tracking. I get paid 2x faster!', 'rating' => 5],
                ['name' => 'Rahul Mehta', 'handle' => '@rahulmehta', 'platform' => 'Instagram • 1.2M followers', 'text' => 'The WhatsApp reminder feature is a game-changer. Brands actually pay on time now. Plus the tax reports save me hours during filing season.', 'rating' => 5],
                ['name' => 'Sneha Iyer', 'handle' => '@snehatech', 'platform' => 'YouTube • 200K subs', 'text' => 'Finally an invoicing tool that understands Indian creators! The automatic CGST/SGST/IGST calculation alone is worth it. My CA is impressed.', 'rating' => 5],
            ] as $testimonial)
                <div class="bg-gray-50 rounded-2xl p-7 lg:p-8 border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < $testimonial['rating']; $i++)
                            <i class="fas fa-star text-yellow-400 text-sm mr-0.5"></i>
                        @endfor
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed mb-6">"{{ $testimonial['text'] }}"</p>
                    <div class="flex items-center">
                        <div class="w-11 h-11 gradient-bg rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ substr($testimonial['name'], 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-gray-900">{{ $testimonial['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $testimonial['platform'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ====================== PRICING ====================== --}}
<section class="py-20 lg:py-28 bg-gray-50" id="pricing">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block px-4 py-1.5 bg-brand-50 text-brand-700 text-sm font-semibold rounded-full border border-brand-200 mb-4">PRICING</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">
                Simple, transparent <span class="gradient-text">pricing</span>
            </h2>
            <p class="mt-5 text-lg text-gray-600">Start free, upgrade when you grow. No hidden fees, no surprises.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-5">
            @foreach(config('invoicehero.plans') as $key => $plan)
                @php
                    $isPopular = $key === 'professional';
                    $features = [
                        'free' => ['5 invoices/month', '3 brands', 'PDF download', 'Basic reports', 'Email support'],
                        'starter' => ['50 invoices/month', '10 brands', '2 team members', 'Email sending', 'Payment links', 'Revenue reports'],
                        'professional' => ['Unlimited invoices', 'Unlimited brands', '5 team members', 'WhatsApp sending', 'Payment gateway', 'Expense tracking', 'Advanced reports', 'Priority support'],
                        'enterprise' => ['Everything in Pro', 'Unlimited team', 'Custom branding', 'API access', 'Dedicated support', 'Custom integrations', 'SLA guarantee'],
                    ];
                @endphp
                <div class="relative {{ $isPopular ? 'lg:-mt-4 lg:mb-4' : '' }}">
                    @if($isPopular)
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-10">
                            <span class="px-4 py-1 gradient-bg text-white text-xs font-bold rounded-full shadow-lg">MOST POPULAR</span>
                        </div>
                    @endif

                    <div class="bg-white rounded-2xl p-6 lg:p-7 border-2 {{ $isPopular ? 'border-brand-500 shadow-xl shadow-brand-500/10' : 'border-gray-100 hover:border-gray-200' }} transition-all duration-300 h-full flex flex-col">
                        <h3 class="text-lg font-bold text-gray-900">{{ $plan['name'] }}</h3>
                        <div class="mt-4 mb-6">
                            @if($plan['price'] == 0)
                                <span class="text-4xl font-black text-gray-900">Free</span>
                                <span class="text-sm text-gray-500 ml-1">forever</span>
                            @else
                                <span class="text-4xl font-black text-gray-900">₹{{ number_format($plan['price']) }}</span>
                                <span class="text-sm text-gray-500 ml-1">/month</span>
                            @endif
                        </div>

                        <ul class="space-y-3 mb-8 flex-1">
                            @foreach($features[$key] as $feat)
                                <li class="flex items-start text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2.5 flex-shrink-0 text-xs"></i>
                                    {{ $feat }}
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('register') }}"
                           class="block text-center py-3 rounded-xl text-sm font-bold transition-all duration-200
                                  {{ $isPopular ? 'gradient-bg text-white shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $plan['price'] == 0 ? 'Get Started Free' : 'Start 14-Day Trial' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ====================== FAQ ====================== --}}
<section class="py-20 lg:py-28 bg-white" id="faq">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1.5 bg-orange-50 text-orange-700 text-sm font-semibold rounded-full border border-orange-200 mb-4">FAQ</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Frequently asked questions</h2>
        </div>

        <div class="space-y-3" x-data="{ open: null }">
            @foreach([
                ['q' => 'Is InvoiceHero really free?', 'a' => 'Yes! Our free plan includes 5 invoices per month, 3 brands, and PDF downloads — completely free forever. No credit card required to sign up.'],
                ['q' => 'How does GST calculation work?', 'a' => 'We auto-detect whether CGST+SGST or IGST should apply based on your state code and the brand\'s state code. Rates are configurable, and TDS deduction is also supported.'],
                ['q' => 'Can I send invoices via WhatsApp?', 'a' => 'Absolutely! With our Professional plan, you can send invoice notifications and payment reminders directly via WhatsApp. Brands can view and pay from the message itself.'],
                ['q' => 'Is my data secure?', 'a' => 'Your data is encrypted, stored securely on Indian servers, and we never share it with anyone. Each creator\'s data is completely isolated from others.'],
                ['q' => 'Can my manager or CA access the account?', 'a' => 'Yes! You can invite team members with different roles — Manager, Accountant, Editor, or Viewer. Each role has specific permissions.'],
                ['q' => 'What payment gateways do you support?', 'a' => 'We support Razorpay and Stripe. You can connect your own gateway accounts so payments go directly to your bank. Manual payment recording is also supported.'],
                ['q' => 'Can I cancel anytime?', 'a' => 'Yes, you can downgrade or cancel your plan anytime. Your data remains accessible, and you can always export everything.'],
            ] as $index => $faq)
                <div class="border border-gray-200 rounded-2xl overflow-hidden transition-all duration-200"
                     :class="open === {{ $index }} ? 'shadow-lg border-brand-200 bg-brand-50/30' : 'hover:border-gray-300'">
                    <button @click="open = open === {{ $index }} ? null : {{ $index }}"
                            class="flex items-center justify-between w-full px-6 py-5 text-left">
                        <span class="text-sm sm:text-base font-semibold text-gray-900 pr-4">{{ $faq['q'] }}</span>
                        <i class="fas text-brand-500 text-sm transition-transform duration-200 flex-shrink-0"
                           :class="open === {{ $index }} ? 'fa-minus rotate-0' : 'fa-plus'"></i>
                    </button>
                    <div x-show="open === {{ $index }}" x-collapse x-cloak>
                        <div class="px-6 pb-5 text-sm text-gray-600 leading-relaxed">{{ $faq['a'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ====================== CTA SECTION ====================== --}}
<section class="py-20 lg:py-24 relative overflow-hidden">
    <div class="absolute inset-0 gradient-bg"></div>
    <div class="absolute inset-0 bg-black/5"></div>
    <div class="absolute top-0 left-0 w-96 h-96 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full translate-x-1/2 translate-y-1/2"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight">
            Ready to get paid on time,<br class="hidden sm:block"> every time?
        </h2>
        <p class="mt-6 text-lg text-white/80 max-w-2xl mx-auto">
            Join thousands of Indian creators who've automated their invoicing. Start free and upgrade when you grow.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-10">
            <a href="{{ route('register') }}" class="w-full sm:w-auto px-10 py-4 bg-white text-brand-600 text-lg font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                Start Free Now <i class="fas fa-arrow-right ml-2"></i>
            </a>
            <a href="{{ route('contact') }}" class="w-full sm:w-auto px-10 py-4 bg-white/10 text-white text-lg font-semibold rounded-2xl border-2 border-white/30 hover:bg-white/20 transition-all duration-300">
                Talk to Us
            </a>
        </div>
        <p class="mt-6 text-sm text-white/60">No credit card required • Free forever plan • Setup in 2 minutes</p>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.13.3/dist/cdn.min.js"></script>
@endpush

@endsection