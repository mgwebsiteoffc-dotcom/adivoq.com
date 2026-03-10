@extends('layouts.tenant')
@section('title','Recurring Invoice')
@section('page_title','Recurring Settings — ' . $invoice->invoice_number)

@section('content')
<a href="{{ route('dashboard.invoices.show', $invoice) }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
    <i class="fas fa-arrow-left mr-1"></i>Back to Invoice
</a>

@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-start justify-between gap-3 mb-4">
        <div>
            <h3 class="text-lg font-black text-gray-900">Enable Recurring</h3>
            <p class="text-sm text-gray-500 mt-1">
                This invoice will act as a template. We will automatically create a new invoice on the next date.
            </p>
        </div>

        @if($invoice->is_recurring)
            <form method="POST" action="{{ route('dashboard.invoices.recurring.destroy', $invoice) }}"
                  onsubmit="return confirm('Disable recurring for this invoice?')">
                @csrf @method('DELETE')
                <button class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-black hover:bg-red-200">
                    Disable
                </button>
            </form>
        @endif
    </div>

    <form method="POST" action="{{ route('dashboard.invoices.recurring.update', $invoice) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Frequency *</label>
            <select name="recurring_frequency" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                @foreach($frequencies as $k => $label)
                    <option value="{{ $k }}" @selected(old('recurring_frequency', $invoice->recurring_frequency) === $k)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Next Invoice Date *</label>
            <input type="date" name="next_recurring_date"
                   value="{{ old('next_recurring_date', $invoice->next_recurring_date?->format('Y-m-d') ?? now()->addMonth()->format('Y-m-d')) }}"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm" required>
        </div>

        <div class="sm:col-span-2 pt-3 border-t flex justify-end gap-2">
            <a href="{{ route('dashboard.invoices.show', $invoice) }}" class="px-5 py-2.5 bg-gray-100 rounded-lg text-sm font-black hover:bg-gray-200">
                Cancel
            </a>
            <button class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">
                Save Recurring
            </button>
        </div>
    </form>
</div>
@endsection