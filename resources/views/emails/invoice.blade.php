<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; background: #f4f4f5; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden;">
        <div style="background: #4F46E5; padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 24px;">Invoice {{ $invoice->invoice_number }}</h1>
        </div>
        <div style="padding: 30px;">
            <p>Dear {{ $invoice->brand->contact_person ?? $invoice->brand->name }},</p>
            <p>Please find your invoice details below:</p>
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr><td style="padding: 8px; border-bottom: 1px solid #eee; color: #666;">Invoice Number</td><td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">{{ $invoice->invoice_number }}</td></tr>
                <tr><td style="padding: 8px; border-bottom: 1px solid #eee; color: #666;">Amount</td><td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">₹{{ number_format($invoice->total_amount, 2) }}</td></tr>
                <tr><td style="padding: 8px; border-bottom: 1px solid #eee; color: #666;">Due Date</td><td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">{{ $invoice->due_date->format('d M Y') }}</td></tr>
            </table>
            @if($invoice->payment_link_token)
                <div style="text-align: center; margin: 25px 0;">
                    <a href="{{ route('payment.link', $invoice->payment_link_token) }}" style="background: #4F46E5; color: white; padding: 14px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">Pay Now →</a>
                </div>
            @endif
            <p style="color: #666; font-size: 14px;">Thank you for your business!</p>
            <p style="font-weight: bold;">{{ $tenant->business_name ?? $tenant->name }}</p>
        </div>
    </div>
</body>
</html>