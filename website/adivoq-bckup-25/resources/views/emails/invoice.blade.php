<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:20px;">

<div style="max-width:600px;margin:auto;background:white;padding:30px;border-radius:8px;">

    <h2>Invoice {{ $invoice->invoice_number }}</h2>

    <p>Hello {{ $invoice->brand->name }},</p>

    <p>Please find attached your invoice.</p>

    <p><strong>Total Amount:</strong> ₹{{ number_format($invoice->total_amount, 2) }}</p>

    <p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>

    <p>
        You can also view this invoice online:
        <br>
        <a href="{{ url('/invoice/' . $invoice->public_token) }}">
            {{ url('/invoice/' . $invoice->public_token) }}
        </a>
    </p>

    <br>

    <p>Thank you.</p>
    <p><strong>AdivoQ</strong></p>

</div>

</body>
</html>