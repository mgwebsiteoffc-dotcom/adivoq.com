@extends('layouts.tenant')
@section('title', $cert ? 'Edit TDS Certificate' : 'Add TDS Certificate')
@section('page_title', $cert ? 'Edit TDS Certificate' : 'Add TDS Certificate')

@section('content')
<a href="{{ route('dashboard.tds-certificates.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
    <i class="fas fa-arrow-left mr-1"></i>Back
</a>

@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl border p-6">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $cert ? route('dashboard.tds-certificates.update',$cert) : route('dashboard.tds-certificates.store') }}"
          class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @if($cert) @method('PUT') @endif

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Financial Year</label>
            <input name="financial_year" value="{{ old('financial_year',$cert->financial_year ?? '') }}" placeholder="2024-25"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Quarter</label>
            <select name="quarter" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                <option value="">—</option>
                @foreach(['Q1','Q2','Q3','Q4'] as $q)
                    <option value="{{ $q }}" @selected(old('quarter',$cert->quarter ?? '')===$q)>{{ $q }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Brand</label>
            <select name="brand_id" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                <option value="">—</option>
                @foreach($brands as $b)
                    <option value="{{ $b->id }}" @selected(old('brand_id',$cert->brand_id ?? '')==$b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Invoice</label>
            <select name="invoice_id" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                <option value="">—</option>
                @foreach($invoices as $inv)
                    <option value="{{ $inv->id }}" @selected(old('invoice_id',$cert->invoice_id ?? '')==$inv->id)>{{ $inv->invoice_number }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Certificate Number</label>
            <input name="certificate_number" value="{{ old('certificate_number',$cert->certificate_number ?? '') }}"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">TDS Amount (₹) *</label>
            <input type="number" step="0.01" min="0" name="tds_amount" value="{{ old('tds_amount',$cert->tds_amount ?? 0) }}" required
                   class="w-full px-3 py-2.5 border rounded-lg text-sm">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Deducted At</label>
            <input type="date" name="deducted_at" value="{{ old('deducted_at',$cert?->deducted_at?->format('Y-m-d')) }}"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Status *</label>
            <select name="status" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                <option value="pending" @selected(old('status',$cert->status ?? 'pending')==='pending')>Pending</option>
                <option value="verified" @selected(old('status',$cert->status ?? '')==='verified')>Verified</option>
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-gray-600 mb-1">Certificate File (PDF/Image)</label>
            @if($cert?->file_path)
                <a class="text-xs font-black text-indigo-600 hover:underline" target="_blank" href="{{ asset('storage/'.$cert->file_path) }}">View current file →</a>
            @endif
            <input type="file" name="file" class="w-full text-sm mt-2">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-gray-600 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-3 py-2.5 border rounded-lg text-sm">{{ old('notes',$cert->notes ?? '') }}</textarea>
        </div>

        <div class="sm:col-span-2 pt-3 border-t flex justify-end gap-2">
            <a href="{{ route('dashboard.tds-certificates.index') }}" class="px-5 py-2.5 bg-gray-100 rounded-lg text-sm font-black">Cancel</a>
            <button class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">
                <i class="fas fa-save mr-2"></i>{{ $cert ? 'Update' : 'Save' }}
            </button>
        </div>
    </form>
</div>
@endsection