@extends('layouts.tenant')
@section('title','Expenses')
@section('page_title','Expenses')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">This Month</p>
        <p class="text-2xl font-bold text-red-600 mt-1">₹{{ number_format($stats['this_month']) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">This Year</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">₹{{ number_format($stats['this_year']) }}</p>
    </div>
</div>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <a href="{{ route('dashboard.expenses.create') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700">
        <i class="fas fa-plus mr-2"></i>Add Expense
    </a>

    <a href="{{ route('dashboard.expense-categories.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">
        Manage Categories
    </a>
</div>

<div class="bg-white rounded-xl border p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
            <select name="category_id" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Campaign</label>
            <select name="campaign_id" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">All</option>
                @foreach($campaigns as $c)
                    <option value="{{ $c->id }}" @selected(request('campaign_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Deductible</label>
            <select name="deductible" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">All</option>
                <option value="1" @selected(request('deductible')==='1')>Yes</option>
                <option value="0" @selected(request('deductible')==='0')>No</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border rounded-lg text-sm">
        </div>

        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Filter</button>
        @if(request()->query())
            <a href="{{ route('dashboard.expenses.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-200">Clear</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Expense</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Category</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Amount</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($expenses as $e)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('dashboard.expenses.show', $e) }}" class="font-bold text-gray-900 hover:text-indigo-600">
                                {{ $e->title }}
                            </a>
                            <div class="text-xs text-gray-500">
                                @if($e->campaign) {{ $e->campaign->name }} @endif
                                @if($e->is_tax_deductible) • <span class="text-green-700 font-semibold">Tax-deductible</span> @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $e->category->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $e->expense_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-right font-black text-red-700">₹{{ number_format($e->amount,2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('dashboard.expenses.edit', $e) }}" class="text-xs font-bold text-blue-600 hover:underline mr-2">Edit</a>
                            <form method="POST" action="{{ route('dashboard.expenses.destroy', $e) }}" class="inline" onsubmit="return confirm('Delete this expense?')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-bold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-14 text-center text-gray-500">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t">{{ $expenses->links() }}</div>
</div>
@endsection