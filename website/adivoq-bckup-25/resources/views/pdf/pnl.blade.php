<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        h2 { margin-bottom: 5px; }
    </style>
</head>
<body>

<h2>Profit & Loss Report</h2>
<p>Period: {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}</p>

<h3>Revenue</h3>
<table>
    <thead>
        <tr>
            <th>Invoice</th>
            <th>Brand</th>
            <th class="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->brand->name ?? '' }}</td>
            <td class="right">₹{{ number_format($invoice->total_amount,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p class="right bold">Total Revenue: ₹{{ number_format($totalRevenue,2) }}</p>

<h3>Expenses</h3>
<table>
    <thead>
        <tr>
            <th>Description</th>
            <th>Date</th>
            <th class="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $expense)
        <tr>
            <td>{{ $expense->description }}</td>
            <td>{{ $expense->expense_date }}</td>
            <td class="right">₹{{ number_format($expense->amount,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p class="right bold">Total Expenses: ₹{{ number_format($totalExpenses,2) }}</p>

<hr>

<h3 class="right">Net Profit: ₹{{ number_format($profit,2) }}</h3>

</body>
</html>