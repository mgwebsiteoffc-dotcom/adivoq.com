@extends('layouts.tenant')

@section('title', $invoice->invoice_number)
@section('page_title', $invoice->invoice_number)

@section('content')
<div class="max-w-4xl space-y-6">

    <a href="{{ route('tenant.invoices.index') }}"
       class="text-surface-400 hover:text-white text-sm">
        ← Back to Invoices
    </a>
    
<div class="flex gap-3">
    <a href="{{ route('tenant.invoices.pdf', $invoice) }}"
       class="px-4 py-2 bg-surface-700 text-white rounded-lg text-sm hover:bg-surface-600">
        Download PDF
    </a>
    @if($invoice->status === 'draft')
<form method="POST"
      action="{{ route('tenant.invoices.send', $invoice->id) }}">
    @csrf
    <button class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
        Mark as Sent
    </button>
</form>
@endif

@if($invoice->brand->phone)
    <form method="POST" action="{{ route('tenant.invoices.whatsapp', $invoice->id) }}" class="inline">
        @csrf
        <button class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
            Send via WhatsApp
        </button>
    </form>
@endif


<form method="POST"
      action="{{ route('tenant.invoices.email', $invoice->id) }}">
    @csrf
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
        Send via Email
    </button>
</form>
</div>




@if($invoice->display_status === 'overdue')
<form method="POST"
      action="{{ route('tenant.invoices.remind', $invoice->id) }}">
    @csrf
    <button class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
        Send Reminder
    </button>
</form>
@endif

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">

        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-white">
                    {{ $invoice->invoice_number }}
                </h2>
                <p class="text-surface-400 text-sm">
                    Brand: {{ $invoice->brand->name }}
                </p>
            </div>

            <div class="text-right">
                <p class="text-surface-400 text-sm">Total</p>
                <p class="text-white font-mono text-lg">
                    ₹{{ number_format($invoice->total_amount, 0) }}
                </p>
            </div>
        </div>

        <div class="mt-6 space-y-2">
            <p class="text-surface-400 text-sm">
                Issue Date: {{ $invoice->issue_date }}
            </p>
            <p class="text-surface-400 text-sm">
                Due Date: {{ $invoice->due_date }}
            </p>
            @php
    $status = $invoice->display_status;
@endphp


<span class="px-3 py-1 rounded-full text-xs font-medium
    @if($status == 'paid') bg-green-500/20 text-green-400
    @elseif($status == 'partially_paid') bg-yellow-500/20 text-yellow-400
    @elseif($status == 'overdue') bg-red-500/20 text-red-400
    @elseif($status == 'sent') bg-blue-500/20 text-blue-400
    @else bg-surface-600 text-surface-300
    @endif">
    {{ ucfirst($status) }}
</span>
        </div>

        <div class="mt-6 border-t border-surface-700 pt-4">
            <h3 class="text-white font-semibold mb-3">Items</h3>

            @foreach($invoice->items as $item)
                <div class="flex justify-between text-sm text-surface-300">
                    <span>{{ $item->description }}</span>
                    <span>₹{{ number_format($item->amount, 0) }}</span>
                </div>
            @endforeach
        </div>

    </div>

</div>
@endsection