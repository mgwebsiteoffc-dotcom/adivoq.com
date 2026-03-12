<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .container { padding: 40px; }
        .header { margin-bottom: 30px; }
        .title { font-size: 32px; font-weight: bold; color: #4F46E5; }
        .meta { margin-top: 5px; font-size: 13px; color: #666; }
        .parties { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .party { width: 48%; }
        .party-label { font-size: 10px; text-transform: uppercase; font-weight: bold; color: #4F46E5; letter-spacing: 1px; margin-bottom: 5px; }
        .party-name { font-size: 15px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th { background: #4F46E5; color: white; padding: 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        tbody td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .totals { width: 250px; margin-left: auto; }
        .totals td { padding: 5px 10px; }
        .total-final { font-size: 16px; font-weight: bold; color: #4F46E5; border-top: 2px solid #4F46E5; }
        .notes { margin-top: 30px; padding: 15px; background: #f9fafb; border-radius: 5px; }
        .notes-label { font-size: 11px; font-weight: bold; margin-bottom: 5px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
<div class="container">
    <table style="margin-bottom: 30px;">
        <tr>
            <td style="border: none; width: 60%; vertical-align: top;">
                <div class="title">INVOICE</div>
                <div class="meta">#{{ $invoice_number }} • {{ \Carbon\Carbon::parse($invoice_date)->format('d M Y') }}</div>
                <div class="meta">Due: {{ \Carbon\Carbon::parse($due_date)->format('d M Y') }}</div>
            </td>
            <td style="border: none; text-align: right; vertical-align: top;"></td>
        </tr>
    </table>

    <table style="margin-bottom: 30px;">
        <tr>
            <td style="border: none; width: 50%; vertical-align: top;">
                <div class="party-label">From</div>
                <div class="party-name">{{ $from['from_name'] ?? '' }}</div>
                <div style="font-size: 11px; color: #666; margin-top: 3px;">
                    @if(!empty($from['from_email'])){{ $from['from_email'] }}<br>@endif
                    @if(!empty($from['from_phone'])){{ $from['from_phone'] }}<br>@endif
                    @if(!empty($from['from_address'])){{ $from['from_address'] }}<br>@endif
                    @if(!empty($from['from_gstin']))<strong>GSTIN:</strong> {{ $from['from_gstin'] }}@endif
                </div>
            </td>
            <td style="border: none; width: 50%; vertical-align: top;">
                <div class="party-label">Bill To</div>
                <div class="party-name">{{ $to['to_name'] ?? '' }}</div>
                <div style="font-size: 11px; color: #666; margin-top: 3px;">
                    @if(!empty($to['to_email'])){{ $to['to_email'] }}<br>@endif
                    @if(!empty($to['to_phone'])){{ $to['to_phone'] }}<br>@endif
                    @if(!empty($to['to_address'])){{ $to['to_address'] }}<br>@endif
                    @if(!empty($to['to_gstin']))<strong>GSTIN:</strong> {{ $to['to_gstin'] }}@endif
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>#</th><th>Description</th><th class="text-right">Qty</th><th class="text-right">Rate</th><th class="text-right">Amount</th></tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item['description'] }}</td>
                <td class="text-right">{{ $item['quantity'] }}</td>
                <td class="text-right">{{ $currency_symbol }}{{ number_format($item['rate'], 2) }}</td>
                <td class="text-right">{{ $currency_symbol }}{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="text-right">{{ $currency_symbol }}{{ number_format($subtotal, 2) }}</td></tr>
        @if($tax_rate > 0)
        <tr><td>Tax ({{ $tax_rate }}%)</td><td class="text-right">{{ $currency_symbol }}{{ number_format($tax_amount, 2) }}</td></tr>
        @endif
        <tr class="total-final"><td style="padding-top: 8px;"><strong>Total</strong></td><td class="text-right" style="padding-top: 8px;"><strong>{{ $currency_symbol }}{{ number_format($total, 2) }}</strong></td></tr>
    </table>

    @if(!empty($notes))
    <div class="notes">
        <div class="notes-label">Notes</div>
        <div style="font-size: 11px; color: #666;">{{ $notes }}</div>
    </div>
    @endif

    <div class="footer">
        Generated with InvoiceHero — Free Invoice Generator | invoicehero.com
    </div>
</div>
</body>
</html>