<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">

<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-8">

    <div class="flex justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Invoice</h1>
            <p class="text-gray-600">{{ $invoice->invoice_number }}</p>
        </div>
        <div class="text-right">
            <p class="text-gray-600">Issue: {{ $invoice->issue_date }}</p>
            <p class="text-gray-600">Due: {{ $invoice->due_date }}</p>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="font-semibold text-gray-700">Bill To:</h2>
        <p>{{ $invoice->brand->name }}</p>
    </div>

    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">Description</th>
                <th class="p-2 text-right">Qty</th>
                <th class="p-2 text-right">Unit</th>
                <th class="p-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr class="border-t">
                <td class="p-2">{{ $item->description }}</td>
                <td class="p-2 text-right">{{ $item->quantity }}</td>
                <td class="p-2 text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                <td class="p-2 text-right">₹{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6 text-right space-y-1 text-sm">
        <p>Subtotal: ₹{{ number_format($invoice->subtotal, 2) }}</p>

        @if($invoice->cgst_amount)
        <p>CGST: ₹{{ number_format($invoice->cgst_amount, 2) }}</p>
        <p>SGST: ₹{{ number_format($invoice->sgst_amount, 2) }}</p>
        @endif

        @if($invoice->igst_amount)
        <p>IGST: ₹{{ number_format($invoice->igst_amount, 2) }}</p>
        @endif

        <p class="font-semibold text-lg">
            Total: ₹{{ number_format($invoice->total_amount, 2) }}
        </p>

        @if($invoice->tds_amount)
        <p class="text-gray-600">
            TDS Deducted: ₹{{ number_format($invoice->tds_amount, 2) }}
        </p>
        @endif

        <p class="text-red-600 font-semibold">
            Balance Due: ₹{{ number_format($invoice->balance_due, 2) }}
        </p>
    </div>

    @if($invoice->notes)
    <div class="mt-6 text-sm text-gray-600">
        <strong>Notes:</strong>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

</div>

</body>
</html>