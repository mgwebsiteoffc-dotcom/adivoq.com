@extends('layouts.tenant')

@section('title','Expenses')
@section('page_title','Expenses')

@section('content')
<div class="space-y-6">

<a href="{{ route('tenant.expenses.create') }}"
   class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
    + Add Expense
</a>

<div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-surface-700/50 text-surface-400">
            <tr>
                <th class="px-6 py-4 text-left">Description</th>
                <th class="px-6 py-4">Category</th>
                <th class="px-6 py-4">Campaign</th>
                <th class="px-6 py-4">Amount</th>
                <th class="px-6 py-4">Date</th>
                <th class="px-6 py-4">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-700">
            @foreach($expenses as $expense)
            <tr>
                <td class="px-6 py-4 text-white">{{ $expense->description }}</td>
                <td class="px-6 py-4 text-surface-400">{{ $expense->category }}</td>
                <td class="px-6 py-4 text-surface-400">
                    {{ $expense->campaign->title ?? '-' }}
                </td>
                <td class="px-6 py-4 text-surface-400 font-mono">
                    ₹{{ number_format($expense->amount,0) }}
                </td>
                <td class="px-6 py-4 text-surface-400">
                    {{ $expense->expense_date }}
                </td>
                <td class="px-6 py-4">
                    <form method="POST"
                          action="{{ route('tenant.expenses.destroy',$expense) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-400 text-sm">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $expenses->links() }}

</div>
@endsection