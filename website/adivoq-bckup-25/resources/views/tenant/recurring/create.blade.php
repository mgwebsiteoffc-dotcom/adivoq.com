@extends('layouts.tenant')

@section('title','Create Recurring Invoice')
@section('page_title','Create Recurring Invoice')

@section('content')
<div class="max-w-xl">

<form method="POST"
      action="{{ route('tenant.recurring.store') }}"
      class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
    @csrf

    <div>
        <label class="block text-sm text-surface-400 mb-1">Brand *</label>
        <select name="brand_id"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm text-surface-400 mb-1">Title *</label>
        <input type="text" name="title"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
    </div>

    <div>
        <label class="block text-sm text-surface-400 mb-1">Amount *</label>
        <input type="number" name="amount"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
    </div>

    <div>
        <label class="block text-sm text-surface-400 mb-1">Frequency *</label>
        <select name="frequency"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
            <option value="monthly">Monthly</option>
            <option value="quarterly">Quarterly</option>
        </select>
    </div>

    <div>
        <label class="block text-sm text-surface-400 mb-1">Start Date *</label>
        <input type="date" name="start_date"
            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
    </div>

    <button class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
        Create Recurring
    </button>

</form>

</div>
@endsection