@extends('layouts.tenant')

@section('title','Tax Summary')
@section('page_title','Tax Summary')

@section('content')

<div class="space-y-10">
<a href="{{ route('tenant.reports.gst.export') }}"
   class="px-6 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
   Export GST CSV
</a>
    <div class="glass rounded-2xl p-8">
        <h3 class="text-xl font-semibold text-white mb-8">
            GST + Income Tax Overview
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-8">

            <div>
                <p class="text-surface-400 text-sm">CGST</p>
                <p class="text-white font-semibold mt-2">₹{{ number_format($cgst,2) }}</p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">SGST</p>
                <p class="text-white font-semibold mt-2">₹{{ number_format($sgst,2) }}</p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">IGST</p>
                <p class="text-white font-semibold mt-2">₹{{ number_format($igst,2) }}</p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">Total GST Collected</p>
                <p class="text-green-400 font-semibold mt-2">₹{{ number_format($gstCollected,2) }}</p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">TDS Deducted</p>
                <p class="text-red-400 font-semibold mt-2">₹{{ number_format($tdsTotal,2) }}</p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">Net Income</p>
                <p class="text-white font-semibold mt-2">₹{{ number_format($netIncome,2) }}</p>
            </div>

        </div>
    </div>

    {{-- Income Tax Section --}}
    <div class="glass rounded-2xl p-8">

        <h3 class="text-lg font-semibold text-white mb-6">
            Income Tax (New Regime Estimate)
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div>
                <p class="text-surface-400 text-sm">Estimated Income Tax</p>
                <p class="text-amber-400 font-semibold mt-2">
                    ₹{{ number_format($estimatedIncomeTax,2) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">TDS Already Paid</p>
                <p class="text-white font-semibold mt-2">
                    ₹{{ number_format($tdsTotal,2) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">Net Tax Liability</p>
                <p class="{{ $netTaxLiability >= 0 ? 'text-red-400' : 'text-green-400' }} font-semibold mt-2">
                    ₹{{ number_format($netTaxLiability,2) }}
                </p>
            </div>

        </div>

    </div>

    {{-- Advance Tax Schedule --}}
    <div class="glass rounded-2xl p-8">

        <h3 class="text-lg font-semibold text-white mb-6">
            Advance Tax Schedule
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">

            @foreach($advanceTax as $label => $amount)
                <div>
                    <p class="text-surface-400 text-xs uppercase">
                        {{ $label }}
                    </p>
                    <p class="text-white font-semibold mt-2">
                        ₹{{ number_format($amount,2) }}
                    </p>
                </div>
            @endforeach

        </div>

    </div>

</div>

@endsection