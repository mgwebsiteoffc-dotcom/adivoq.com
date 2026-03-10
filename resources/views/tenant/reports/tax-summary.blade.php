@extends('layouts.tenant')
@section('title','Tax Summary')
@section('page_title','Tax Summary')

@section('content')
<div class="bg-white rounded-xl border p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Year</label>
            <input type="number" name="year" value="{{ request('year', $year) }}" min="2000" max="2100"
                   class="px-3 py-2 border rounded-lg text-sm w-32">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">Apply</button>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">CGST</p>
        <p class="text-xl font-black text-gray-900 mt-1">₹{{ number_format($gst['cgst'],2) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">SGST</p>
        <p class="text-xl font-black text-gray-900 mt-1">₹{{ number_format($gst['sgst'],2) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">IGST</p>
        <p class="text-xl font-black text-gray-900 mt-1">₹{{ number_format($gst['igst'],2) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">GST Total</p>
        <p class="text-xl font-black text-green-700 mt-1">₹{{ number_format($gst['total'],2) }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border p-6">
    <h3 class="text-sm font-black text-gray-900 mb-4">TDS</h3>
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">Total TDS deducted (Paid/Partially paid invoices)</p>
        <p class="text-2xl font-black text-red-700">₹{{ number_format($tds['total'],2) }}</p>
    </div>

    <div class="mt-4 text-xs text-gray-500 bg-gray-50 p-4 rounded-lg">
        <i class="fas fa-info-circle mr-1"></i>
        This is a summary for tracking. Please verify with your CA before filing.
    </div>
</div>
@endsection