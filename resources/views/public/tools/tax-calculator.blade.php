@extends('layouts.public')
@section('title', 'Free Tax Calculator for Creators — InvoiceHero')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block px-4 py-1.5 bg-green-50 text-green-700 text-sm font-semibold rounded-full border border-green-200 mb-4">FREE TOOL</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">Creator <span class="gradient-text">Tax Calculator</span></h1>
            <p class="mt-4 text-lg text-gray-600">Calculate your income tax (Old vs New regime), GST, and TDS — all in one place.</p>
        </div>

        <form method="POST" action="{{ route('tools.calculate-tax') }}" class="space-y-8">
            @csrf

            {{-- Income Tax Section --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-coins text-brand-600"></i>
                    </div>
                    Income Tax Calculator
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Annual Income (₹)</label>
                        <input type="number" name="income" value="{{ old('income', request('income')) }}" placeholder="e.g., 1200000"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-lg font-semibold focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    </div>
                </div>
            </div>

            {{-- GST Section --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-percentage text-green-600"></i>
                    </div>
                    GST Calculator
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (₹)</label>
                        <input type="number" name="gst_amount" value="{{ old('gst_amount') }}" placeholder="100000"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">GST Rate (%)</label>
                        <select name="gst_rate" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                            <option value="">Select</option>
                            @foreach([5, 12, 18, 28] as $rate)
                                <option value="{{ $rate }}" {{ old('gst_rate') == $rate ? 'selected' : '' }}>{{ $rate }}%</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Same State?</label>
                        <select name="same_state" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                            <option value="1">Yes (CGST + SGST)</option>
                            <option value="0">No (IGST)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- TDS Section --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-cut text-orange-600"></i>
                    </div>
                    TDS Calculator
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Invoice Amount (₹)</label>
                        <input type="number" name="tds_amount" value="{{ old('tds_amount') }}" placeholder="100000"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">TDS Rate (%)</label>
                        <select name="tds_rate" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                            <option value="">Select</option>
                            @foreach([1, 2, 5, 10, 20] as $rate)
                                <option value="{{ $rate }}" {{ old('tds_rate') == $rate ? 'selected' : '' }}>{{ $rate }}%</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full sm:w-auto px-10 py-4 gradient-bg text-white text-lg font-bold rounded-2xl shadow-xl hover:shadow-2xl transition-all">
                <i class="fas fa-calculator mr-2"></i>Calculate Now
            </button>
        </form>

        {{-- Results --}}
        @if(isset($results))
            <div class="mt-12 space-y-8" id="results">
                {{-- Income Tax Results --}}
                @if(isset($results['old_regime']) && isset($results['new_regime']))
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6"><i class="fas fa-chart-bar text-brand-600 mr-2"></i>Income Tax Comparison</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach(['old_regime' => $results['old_regime'], 'new_regime' => $results['new_regime']] as $key => $regime)
                                @php $isBetter = $regime['total_tax'] <= ($key === 'old_regime' ? $results['new_regime']['total_tax'] : $results['old_regime']['total_tax']); @endphp
                                <div class="rounded-xl border-2 p-6 {{ $isBetter ? 'border-green-300 bg-green-50' : 'border-gray-200' }}">
                                    @if($isBetter)
                                        <span class="inline-block text-xs px-3 py-1 bg-green-200 text-green-800 font-bold rounded-full mb-3">✨ BETTER OPTION</span>
                                    @endif
                                    <h4 class="text-lg font-bold text-gray-900">{{ $regime['regime'] }}</h4>
                                    <div class="mt-4 space-y-2 text-sm">
                                        <div class="flex justify-between"><span class="text-gray-600">Income</span><span class="font-medium">₹{{ number_format($regime['income']) }}</span></div>
                                        <div class="flex justify-between"><span class="text-gray-600">Tax (before cess)</span><span class="font-medium">₹{{ number_format($regime['tax_before_cess']) }}</span></div>
                                        <div class="flex justify-between"><span class="text-gray-600">Health & Edu Cess (4%)</span><span class="font-medium">₹{{ number_format($regime['cess']) }}</span></div>
                                        <hr>
                                        <div class="flex justify-between text-base font-bold"><span>Total Tax</span><span class="text-red-600">₹{{ number_format($regime['total_tax']) }}</span></div>
                                        <div class="flex justify-between"><span class="text-gray-600">Effective Rate</span><span class="font-medium">{{ $regime['effective_rate'] }}%</span></div>
                                        <div class="flex justify-between"><span class="text-gray-600">Take Home</span><span class="font-bold text-green-600">₹{{ number_format($regime['income'] - $regime['total_tax']) }}</span></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 p-4 bg-brand-50 rounded-xl text-sm text-brand-800">
                            <i class="fas fa-lightbulb mr-2"></i>
                            <strong>You save ₹{{ number_format(abs($results['old_regime']['total_tax'] - $results['new_regime']['total_tax'])) }}</strong>
                            with the {{ $results['old_regime']['total_tax'] <= $results['new_regime']['total_tax'] ? 'Old' : 'New' }} Regime.
                        </div>
                    </div>
                @endif

                {{-- GST Results --}}
                @if(isset($results['gst']))
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-percentage text-green-600 mr-2"></i>GST Breakdown</h3>
                        <div class="bg-gray-50 rounded-xl p-5">
                            @if($results['gst']['type'] === 'intra_state')
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between"><span>CGST ({{ $results['gst']['cgst_rate'] }}%)</span><span class="font-medium">₹{{ number_format($results['gst']['cgst_amount'], 2) }}</span></div>
                                    <div class="flex justify-between"><span>SGST ({{ $results['gst']['sgst_rate'] }}%)</span><span class="font-medium">₹{{ number_format($results['gst']['sgst_amount'], 2) }}</span></div>
                                </div>
                            @else
                                <div class="text-sm"><div class="flex justify-between"><span>IGST ({{ $results['gst']['igst_rate'] }}%)</span><span class="font-medium">₹{{ number_format($results['gst']['igst_amount'], 2) }}</span></div></div>
                            @endif
                            <hr class="my-3">
                            <div class="flex justify-between text-base font-bold"><span>Total GST</span><span>₹{{ number_format($results['gst']['total_gst'], 2) }}</span></div>
                        </div>
                    </div>
                @endif

                {{-- TDS Results --}}
                @if(isset($results['tds']))
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4"><i class="fas fa-cut text-orange-600 mr-2"></i>TDS Breakdown</h3>
                        <div class="bg-gray-50 rounded-xl p-5 space-y-2 text-sm">
                            <div class="flex justify-between"><span>TDS Rate</span><span class="font-medium">{{ $results['tds']['tds_rate'] }}%</span></div>
                            <div class="flex justify-between"><span>TDS Deducted</span><span class="font-medium text-red-600">₹{{ number_format($results['tds']['tds_amount'], 2) }}</span></div>
                            <hr class="my-2">
                            <div class="flex justify-between text-base font-bold"><span>Net Receivable</span><span class="text-green-600">₹{{ number_format($results['tds']['net_amount'], 2) }}</span></div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- CTA --}}
        <div class="mt-16 text-center bg-gray-50 rounded-2xl p-8 border border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Want automatic tax calculation in your invoices?</h3>
            <p class="text-gray-600 mb-5">InvoiceHero auto-calculates GST and TDS on every invoice.</p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 gradient-bg text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition">
                Start Free <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endsection