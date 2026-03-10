<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .h1 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .muted { color:#666; }
        .box { border:1px solid #eee; padding:12px; border-radius:8px; margin-top:12px; }
        .row { display:flex; justify-content: space-between; padding:6px 0; border-bottom:1px solid #f2f2f2; }
        .row:last-child { border-bottom:0; }
        .label { color:#666; }
        .val { font-weight:700; }
    </style>
</head>
<body>
    <div class="h1">Tax Summary ({{ $year }})</div>
    <a href="{{ route('dashboard.reports.pdf', ['type'=>'tax-summary'] + request()->query()) }}">Download PDF</a>
    <div class="muted">{{ $tenant->business_name ?? $tenant->name }}</div>

    <div class="box">
        <div class="row"><div class="label">CGST</div><div class="val">₹{{ number_format($gst['cgst'],2) }}</div></div>
        <div class="row"><div class="label">SGST</div><div class="val">₹{{ number_format($gst['sgst'],2) }}</div></div>
        <div class="row"><div class="label">IGST</div><div class="val">₹{{ number_format($gst['igst'],2) }}</div></div>
        <div class="row"><div class="label">GST Total</div><div class="val">₹{{ number_format($gst['total'],2) }}</div></div>
    </div>

    <div class="box">
        <div class="row"><div class="label">TDS Total (paid/partial)</div><div class="val">₹{{ number_format($tds['total'],2) }}</div></div>
    </div>
</body>
</html>