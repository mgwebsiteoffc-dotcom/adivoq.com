@extends('layouts.tenant')

@section('title', 'Tax Summary')
@section('page_title', 'Tax Summary')

@section('content')

<div class="space-y-8">

    {{-- Date Filter --}}
    <form method="GET"
          action="{{ route('tenant.tax') }}"
          class="glass rounded-2xl p-6 flex gap-6 items-end">

        <div>
            <label class="text-sm text-surface-400">Start Date</label>
            <input type="date" name="start_date"
                   value="{{ $start->format('Y-m-d') }}"
                   class="bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-white text-sm">
        </div>

        <div>
            <label class="text-sm text-surface-400">End Date</label>
            <input type="date" name="end_date"
                   value="{{ $end->format('Y-m-d') }}"
                   class="bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-white text-sm">
        </div>

        <button class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
            Filter
        </button>

    </form>
    
    @if($netTaxLiability > 10000)
<form method="POST" action="{{ route('tenant.tax.remind') }}">
    @csrf
    <button class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm">
        Send Reminder Email
    </button>
</form>
@endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="glass rounded-2xl p-6">
            <p class="text-surface-400 text-sm">Total Revenue</p>
            <p class="text-green-400 text-xl font-semibold mt-2">
                ₹{{ number_format($totalRevenue,2) }}
            </p>
        </div>

        <div class="glass rounded-2xl p-6">
            <p class="text-surface-400 text-sm">Total Expenses</p>
            <p class="text-red-400 text-xl font-semibold mt-2">
                ₹{{ number_format($totalExpenses,2) }}
            </p>
        </div>

        <div class="glass rounded-2xl p-6">
            <p class="text-surface-400 text-sm">Net Income</p>
            <p class="text-white text-xl font-semibold mt-2">
                ₹{{ number_format($netIncome,2) }}
            </p>
        </div>

    </div>

    {{-- GST & TDS --}}
    <div class="glass rounded-2xl p-6 space-y-6">

        <div>
            <p class="text-surface-400 text-sm">GST Collected</p>
            <p class="text-amber-400 text-lg font-semibold mt-2">
                ₹{{ number_format($gstTotal,2) }}
            </p>
        </div>

        <div>
            <p class="text-surface-400 text-sm">TDS Deducted</p>
            <p class="text-red-400 text-lg font-semibold mt-2">
                ₹{{ number_format($tdsTotal,2) }}
            </p>
        </div>

    </div>

    {{-- Income Tax --}}
    <div class="glass rounded-2xl p-6 space-y-4">

        <div>
            <p class="text-surface-400 text-sm">Estimated Income Tax</p>
            <p class="text-amber-400 text-lg font-semibold mt-2">
                ₹{{ number_format($estimatedTax,2) }}
            </p>
        </div>

        <div>
            <p class="text-surface-400 text-sm">Net Tax Liability</p>
            <p class="{{ $netTaxLiability >= 0 ? 'text-red-400' : 'text-green-400' }} text-lg font-semibold mt-2">
                ₹{{ number_format($netTaxLiability,2) }}
            </p>
        </div>

    </div>

</div>

@endsection