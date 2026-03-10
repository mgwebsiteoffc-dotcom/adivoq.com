@extends('layouts.tenant')
@section('title','Reports')
@section('page_title','Reports')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">Revenue (This Month)</p>
        <p class="text-2xl font-black text-green-700 mt-1">₹{{ number_format($stats['revenue_this_month']) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">Outstanding</p>
        <p class="text-2xl font-black text-red-700 mt-1">₹{{ number_format($stats['outstanding']) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">Expenses (This Month)</p>
        <p class="text-2xl font-black text-gray-900 mt-1">₹{{ number_format($stats['expenses_this_month']) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    @foreach([
        ['title'=>'Revenue Report','route'=>'dashboard.reports.revenue','desc'=>'Monthly revenue + brand breakdown'],
        ['title'=>'Invoice Aging','route'=>'dashboard.reports.invoice-aging','desc'=>'Outstanding invoices by overdue bucket'],
        ['title'=>'Payment Collection','route'=>'dashboard.reports.payment-collection','desc'=>'Payments by method and list'],
        ['title'=>'Expenses Report','route'=>'dashboard.reports.expenses','desc'=>'Expenses by category + list'],
        ['title'=>'Profit & Loss','route'=>'dashboard.reports.profit-loss','desc'=>'Revenue - Expenses by month'],
        ['title'=>'Tax Summary','route'=>'dashboard.reports.tax-summary','desc'=>'GST and TDS totals for year'],
    ] as $card)
        <a href="{{ route($card['route']) }}" class="bg-white rounded-xl border p-6 hover:shadow-md transition">
            <h3 class="text-lg font-black text-gray-900">{{ $card['title'] }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $card['desc'] }}</p>
            <p class="text-sm font-bold text-indigo-600 mt-3">Open →</p>
        </a>
    @endforeach
</div>
@endsection