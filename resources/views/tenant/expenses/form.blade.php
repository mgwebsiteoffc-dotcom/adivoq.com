@extends('layouts.tenant')
@section('title', $expense ? 'Edit Expense' : 'Add Expense')
@section('page_title', $expense ? 'Edit Expense' : 'Add Expense')

@section('content')
<a href="{{ $expense ? route('dashboard.expenses.show',$expense) : route('dashboard.expenses.index') }}"
   class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
    <i class="fas fa-arrow-left mr-1"></i>Back
</a>

@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl border p-6">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $expense ? route('dashboard.expenses.update',$expense) : route('dashboard.expenses.store') }}"
          class="space-y-4">
        @csrf
        @if($expense) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Title *</label>
                <input name="title" value="{{ old('title',$expense->title ?? '') }}" required
                       class="w-full px-3 py-2.5 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Amount (₹) *</label>
                <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount',$expense->amount ?? '') }}" required
                       class="w-full px-3 py-2.5 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Date *</label>
                <input type="date" name="expense_date" value="{{ old('expense_date', ($expense?->expense_date?->format('Y-m-d') ?? now()->format('Y-m-d'))) }}" required
                       class="w-full px-3 py-2.5 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                <select name="expense_category_id" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                    <option value="">None</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('expense_category_id',$expense->expense_category_id ?? '')==$cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Campaign</label>
                <select name="campaign_id" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                    <option value="">None</option>
                    @foreach($campaigns as $c)
                        <option value="{{ $c->id }}" @selected(old('campaign_id',$expense->campaign_id ?? '')==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2.5 border rounded-lg text-sm">{{ old('description',$expense->description ?? '') }}</textarea>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <input type="checkbox" name="is_tax_deductible" value="1" class="rounded"
                       @checked(old('is_tax_deductible', $expense->is_tax_deductible ?? false))>
                <span class="text-sm text-gray-700 font-semibold">Tax deductible</span>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Receipt</label>
                @if($expense?->receipt_path)
                    <a href="{{ asset('storage/'.$expense->receipt_path) }}" target="_blank"
                       class="inline-block text-xs font-semibold text-indigo-600 hover:underline mb-2">
                        View current receipt →
                    </a>
                @endif
                <input type="file" name="receipt" class="w-full text-sm text-gray-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2.5 border rounded-lg text-sm">{{ old('notes',$expense->notes ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('dashboard.expenses.index') }}" class="px-5 py-2.5 bg-gray-100 rounded-lg text-sm font-bold hover:bg-gray-200">Cancel</a>
            <button class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">
                <i class="fas fa-save mr-2"></i>{{ $expense ? 'Update' : 'Add' }} Expense
            </button>
        </div>
    </form>
</div>
@endsection