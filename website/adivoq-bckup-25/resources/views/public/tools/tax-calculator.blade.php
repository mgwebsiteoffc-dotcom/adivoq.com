<!-- resources/views/public/tools/tax-calculator.blade.php -->
@extends('layouts.public')

@section('title', 'Free Tax Calculator for Creators — AdivoQ')
@section('meta_description', 'Calculate GST, TDS, and estimated income tax for content creators in India. Free tool by AdivoQ.')

@section('content')
<div class="py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-white">Creator Tax Calculator</h1>
            <p class="mt-4 text-surface-400 max-w-2xl mx-auto">Calculate GST, TDS deductions, and estimated income tax. Built specifically for Indian content creators.</p>
        </div>

        {{-- Calculator --}}
        <div class="bg-surface-800 border border-surface-700 rounded-2xl p-6 sm:p-8"
             x-data="{
                monthlyIncome: 100000,
                gstRate: 18,
                tdsSection: '194J',
                regime: 'new',

                tdsRates: {
                    '194J': 10,
                    '194C': 2,
                    '194H': 5,
                    '194O': 1
                },

                get annualIncome() {
                    return (parseFloat(this.monthlyIncome) || 0) * 12;
                },

                get gstPerMonth() {
                    return ((parseFloat(this.monthlyIncome) || 0) * parseFloat(this.gstRate)) / 100;
                },

                get tdsRate() {
                    return this.tdsRates[this.tdsSection] || 10;
                },

                get tdsPerMonth() {
                    return ((parseFloat(this.monthlyIncome) || 0) * this.tdsRate) / 100;
                },

                get inHandPerMonth() {
                    return (parseFloat(this.monthlyIncome) || 0) + this.gstPerMonth - this.tdsPerMonth;
                },

                get annualTds() {
                    return this.tdsPerMonth * 12;
                },

                get estimatedTax() {
                    const income = this.annualIncome;
                    if (this.regime === 'new') {
                        return this.calculateNewRegime(income);
                    }
                    return this.calculateOldRegime(income);
                },

                get taxPayable() {
                    return this.estimatedTax - this.annualTds;
                },

                calculateNewRegime(income) {
                    if (income <= 300000) return 0;
                    if (income <= 700000) return (income - 300000) * 0.05;
                    if (income <= 1000000) return 20000 + (income - 700000) * 0.10;
                    if (income <= 1200000) return 50000 + (income - 1000000) * 0.15;
                    if (income <= 1500000) return 80000 + (income - 1200000) * 0.20;
                    return 140000 + (income - 1500000) * 0.30;
                },

                calculateOldRegime(income) {
                    if (income <= 250000) return 0;
                    if (income <= 500000) return (income - 250000) * 0.05;
                    if (income <= 1000000) return 12500 + (income - 500000) * 0.20;
                    return 112500 + (income - 1000000) * 0.30;
                },

                formatNumber(num) {
                    return parseFloat(num || 0).toLocaleString('en-IN', {
                        maximumFractionDigits: 0
                    });
                }
             }">

            {{-- Input Section --}}
            <div class="grid md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Enter Your Details
                    </h3>

                    {{-- Monthly Income --}}
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">Monthly Income (₹)</label>
                        <input
                            type="number"
                            x-model="monthlyIncome"
                            placeholder="100000"
                            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-3 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-lg font-mono"
                        >
                        <p class="text-surface-500 text-xs mt-1">Average monthly income from all brand deals</p>
                    </div>

                    {{-- GST Rate --}}
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">GST Rate (%)</label>
                        <select
                            x-model="gstRate"
                            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-3 text-surface-100 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all"
                        >
                            <option value="0">0% (Not GST Registered)</option>
                            <option value="5">5%</option>
                            <option value="12">12%</option>
                            <option value="18" selected>18% (Standard for Services)</option>
                            <option value="28">28%</option>
                        </select>
                    </div>

                    {{-- TDS Section --}}
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1.5">TDS Section (Deducted by Brands)</label>
                        <select
                            x-model="tdsSection"
                            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-3 text-surface-100 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all"
                        >
                            <option value="194J">194J - Professional Services (10%)</option>
                            <option value="194C">194C - Contractor (2%)</option>
                            <option value="194H">194H - Commission (5%)</option>
                            <option value="194O">194O - E-commerce (1%)</option>
                        </select>
                        <p class="text-surface-500 text-xs mt-1">Most brands deduct TDS under 194J at 10%</p>
                    </div>

                    {{-- Tax Regime --}}
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-3">Income Tax Regime</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="regime" value="new" class="w-4 h-4 text-brand-600 bg-surface-900 border-surface-600 focus:ring-brand-500">
                                <span class="text-surface-300 text-sm">New Regime</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="regime" value="old" class="w-4 h-4 text-brand-600 bg-surface-900 border-surface-600 focus:ring-brand-500">
                                <span class="text-surface-300 text-sm">Old Regime</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Results Section --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Tax Breakdown
                    </h3>

                    {{-- Results Cards --}}
                    <div class="space-y-3">
                        {{-- Annual Income --}}
                        <div class="bg-surface-700/50 rounded-xl p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-surface-400 text-sm">Annual Income</span>
                                <span class="text-white font-semibold font-mono" x-text="'₹' + formatNumber(annualIncome)"></span>
                            </div>
                        </div>

                        {{-- GST Collectible --}}
                        <div class="bg-surface-700/50 rounded-xl p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-surface-400 text-sm">GST Collectible (per month)</span>
                                    <p class="text-surface-500 text-xs">You collect this from brands & pay to govt</p>
                                </div>
                                <span class="text-emerald-400 font-semibold font-mono" x-text="'₹' + formatNumber(gstPerMonth)"></span>
                            </div>
                        </div>

                        {{-- TDS Deducted --}}
                        <div class="bg-surface-700/50 rounded-xl p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-surface-400 text-sm">TDS Deducted (per month)</span>
                                    <p class="text-surface-500 text-xs">Brands deduct this before paying you</p>
                                </div>
                                <span class="text-amber-400 font-semibold font-mono" x-text="'₹' + formatNumber(tdsPerMonth)"></span>
                            </div>
                        </div>

                        {{-- In-Hand Amount --}}
                        <div class="bg-surface-700/50 rounded-xl p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-surface-400 text-sm">You Receive (per month)</span>
                                    <p class="text-surface-500 text-xs">Income + GST - TDS</p>
                                </div>
                                <span class="text-white font-semibold font-mono" x-text="'₹' + formatNumber(inHandPerMonth)"></span>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-surface-600 my-4"></div>

                        {{-- Annual TDS --}}
                        <div class="bg-surface-700/50 rounded-xl p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-surface-400 text-sm">Total TDS (Annual)</span>
                                <span class="text-amber-400 font-semibold font-mono" x-text="'₹' + formatNumber(annualTds)"></span>
                            </div>
                        </div>

                        {{-- Estimated Income Tax --}}
                        <div class="bg-brand-600/20 border border-brand-500/30 rounded-xl p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-white text-sm font-medium">Estimated Income Tax</span>
                                    <p class="text-surface-400 text-xs" x-text="regime === 'new' ? 'New Regime (FY 2024-25)' : 'Old Regime (without deductions)'"></p>
                                </div>
                                <span class="text-brand-400 font-bold text-xl font-mono" x-text="'₹' + formatNumber(estimatedTax)"></span>
                            </div>
                        </div>

                        {{-- Tax Payable/Refund --}}
                        <div class="rounded-xl p-4"
                             :class="taxPayable >= 0 ? 'bg-red-500/10 border border-red-500/20' : 'bg-emerald-500/10 border border-emerald-500/20'">
                            <div class="flex justify-between items-center">
                                <span class="text-sm"
                                      :class="taxPayable >= 0 ? 'text-red-400' : 'text-emerald-400'"
                                      x-text="taxPayable >= 0 ? 'Additional Tax to Pay' : 'Expected Refund'"></span>
                                <span class="font-bold text-xl font-mono"
                                      :class="taxPayable >= 0 ? 'text-red-400' : 'text-emerald-400'"
                                      x-text="'₹' + formatNumber(Math.abs(taxPayable))"></span>
                            </div>
                            <p class="text-surface-500 text-xs mt-1"
                               x-text="taxPayable >= 0 ? 'Pay via advance tax quarterly' : 'Claim while filing ITR'"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Disclaimer --}}
            <div class="mt-8 p-4 bg-surface-700/30 rounded-xl">
                <p class="text-surface-500 text-xs">
                    <strong class="text-surface-400">Disclaimer:</strong> This calculator provides estimates only. Actual tax liability depends on various factors including deductions, exemptions, and other income sources. Consult a qualified CA for accurate tax planning.
                </p>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="grid md:grid-cols-3 gap-6 mt-12">
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
                <div class="w-10 h-10 bg-brand-600/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="font-semibold text-white mb-2">What is TDS?</h4>
                <p class="text-surface-400 text-sm">Tax Deducted at Source. Brands deduct this from your payment and deposit to the government on your behalf.</p>
            </div>

            <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
                <div class="w-10 h-10 bg-brand-600/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <h4 class="font-semibold text-white mb-2">Do I need GST?</h4>
                <p class="text-surface-400 text-sm">GST registration is mandatory if your annual turnover exceeds ₹20 lakhs (₹10 lakhs in special states).</p>
            </div>

            <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
                <div class="w-10 h-10 bg-brand-600/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="font-semibold text-white mb-2">Advance Tax</h4>
                <p class="text-surface-400 text-sm">If your tax liability exceeds ₹10,000, pay advance tax quarterly (Jun 15, Sep 15, Dec 15, Mar 15).</p>
            </div>
        </div>

        {{-- CTA --}}
        <div class="mt-12 text-center">
            <p class="text-surface-400 mb-4">Want automatic tax calculations on every invoice?</p>
            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition-colors">
                Try AdivoQ Free →
            </a>
        </div>
    </div>
</div>
@endsection