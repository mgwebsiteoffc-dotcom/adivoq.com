<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .h1 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .muted { color:#666; }
        table { width:100%; border-collapse: collapse; margin-top: 14px; }
        th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
        th { background:#f4f4f5; font-size: 11px; text-transform: uppercase; color:#444; }
        .right { text-align:right; }
        .total { font-weight: 700; }
    </style>
</head>
<body>
    <div class="h1">Profit & Loss Report</div>
    <a href="{{ route('dashboard.reports.pdf', ['type'=>'profit-loss'] + request()->query()) }}">Download PDF</a>

    <div class="muted">{{ $tenant->business_name ?? $tenant->name }} • Period: {{ $from->toDateString() }} to {{ $to->toDateString() }}</div>

    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="right">Revenue</th>
                <th class="right">Expense</th>
                <th class="right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($months as $m)
                <tr>
                    <td>{{ $m['month'] }}</td>
                    <td class="right">₹{{ number_format($m['revenue'],2) }}</td>
                    <td class="right">₹{{ number_format($m['expense'],2) }}</td>
                    <td class="right">₹{{ number_format($m['profit'],2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="total">Total</td>
                <td class="right total">₹{{ number_format($totals['revenue'],2) }}</td>
                <td class="right total">₹{{ number_format($totals['expense'],2) }}</td>
                <td class="right total">₹{{ number_format($totals['profit'],2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>