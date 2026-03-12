<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .total { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

<h2>Invoice: {{ $invoice->invoice_number }}</h2>

<p><strong>Issue Date:</strong> {{ $invoice->issue_date }}</p>
<p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>

<h3>Bill To:</h3>
<p>{{ $invoice->brand->name }}</p>

<table>
    <thead>
        <tr>
            <th>Description</th>
            <th class="right">Qty</th>
            <th class="right">Unit</th>
            <th class="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">₹{{ number_format($item->unit_price, 2) }}</td>
            <td class="right">₹{{ number_format($item->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br>

<p class="right">Subtotal: ₹{{ number_format($invoice->subtotal, 2) }}</p>

@if($invoice->cgst_amount)
<p class="right">CGST: ₹{{ number_format($invoice->cgst_amount, 2) }}</p>
<p class="right">SGST: ₹{{ number_format($invoice->sgst_amount, 2) }}</p>
@endif

@if($invoice->igst_amount)
<p class="right">IGST: ₹{{ number_format($invoice->igst_amount, 2) }}</p>
@endif

@if($invoice->tds_amount)
<p class="right">TDS: ₹{{ number_format($invoice->tds_amount, 2) }}</p>
@endif

<p class="right total">Total: ₹{{ number_format($invoice->total_amount, 2) }}</p>
<p class="right bold">Balance Due: ₹{{ number_format($invoice->balance_due, 2) }}</p>

</body>
</html>