@extends('layouts.tenant')
@section('title','Expense')
@section('page_title','Expense Details')

@section('content')
<a href="{{ route('dashboard.expenses.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
    <i class="fas fa-arrow-left mr-1"></i>Back
</a>

<div class="bg-white rounded-xl border p-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-900">{{ $expense->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ $expense->expense_date->format('M d, Y') }}
                @if($expense->category) • {{ $expense->category->name }} @endif
                @if($expense->campaign) • {{ $expense->campaign->name }} @endif
            </p>
            @if($expense->is_tax_deductible)
                <span class="inline-block mt-2 px-3 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">Tax Deductible</span>
            @endif
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Amount</p>
            <p class="text-3xl font-black text-red-700">₹{{ number_format($expense->amount,2) }}</p>
        </div>
    </div>

    @if($expense->description)
        <div class="mt-5 text-sm text-gray-700 bg-gray-50 rounded-lg p-4">{{ $expense->description }}</div>
    @endif

    @if($expense->receipt_path)
        <div class="mt-5">
            <a href="{{ asset('storage/'.$expense->receipt_path) }}" target="_blank"
               class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-lg text-sm font-bold hover:bg-gray-200">
                <i class="fas fa-receipt mr-2"></i>View Receipt
            </a>
        </div>
    @endif

    <div class="mt-6 flex gap-2">
        <a href="{{ route('dashboard.expenses.edit',$expense) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Edit</a>
        <form method="POST" action="{{ route('dashboard.expenses.destroy',$expense) }}" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-bold hover:bg-red-200">Delete</button>
        </form>
    </div>
</div>
@endsection