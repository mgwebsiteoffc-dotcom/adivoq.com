@extends('layouts.tenant')

@section('title','Add Expense')
@section('page_title','Add Expense')

@section('content')
<div class="max-w-xl">

<form method="POST"
      action="{{ route('tenant.expenses.store') }}"
      class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
    @csrf

    <div>
        <label class="block text-sm text-surface-400 mb-1">Description *</label>
        <input type="text" name="description"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
    </div>

    <div>
        <label class="block text-sm text-surface-400 mb-1">Category *</label>
        <select name="category"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
            <option value="equipment">Equipment</option>
            <option value="software">Software</option>
            <option value="travel">Travel</option>
            <option value="outsourcing">Outsourcing</option>
            <option value="ads">Advertising</option>
            <option value="other">Other</option>
        </select>
    </div>

    <div>
        <label class="block text-sm text-surface-400 mb-1">Campaign</label>
        <select name="campaign_id"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
            <option value="">None</option>
            @foreach($campaigns as $campaign)
                <option value="{{ $campaign->id }}">{{ $campaign->title }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm text-surface-400 mb-1">Amount *</label>
        <input type="number" name="amount"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
    </div>

    <div>
        <label class="block text-sm text-surface-400 mb-1">Expense Date *</label>
        <input type="date" name="expense_date"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
    </div>

    <button class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
        Save Expense
    </button>

</form>

</div>
@endsection