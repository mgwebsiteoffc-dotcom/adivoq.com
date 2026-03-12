<!DOCTYPE html>
<html>
<body style="font-family: Arial; padding:20px; background:#f5f5f5;">

<div style="max-width:600px;margin:auto;background:white;padding:30px;border-radius:8px;">

<h2>Payment Reminder</h2>

<p>Hello {{ $invoice->brand->name }},</p>

<p>This is a friendly reminder that invoice <strong>{{ $invoice->invoice_number }}</strong> is due.</p>

<p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>
<p><strong>Balance Due:</strong> ₹{{ number_format($invoice->balance_due,2) }}</p>

<p>
You can view the invoice here:
<br>
<a href="{{ url('/invoice/'.$invoice->public_token) }}">
    {{ url('/invoice/'.$invoice->public_token) }}
</a>
</p>

<br>

<p>Thank you.</p>
<p><strong>AdivoQ</strong></p>

</div>

</body>
</html>