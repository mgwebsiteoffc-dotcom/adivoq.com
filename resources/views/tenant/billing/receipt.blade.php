<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subscription Receipt - {{ $payment->razorpay_payment_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .receipt { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
        .company-info { margin-bottom: 20px; }
        .payment-details { margin: 20px 0; }
        .payment-details table { width: 100%; border-collapse: collapse; }
        .payment-details th, .payment-details td { padding: 8px; text-align: left; border-bottom: 1px solid #eee; }
        .total { font-weight: bold; font-size: 18px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>Payment Receipt</h1>
            <p>InvoiceHero Subscription</p>
        </div>

        <div class="company-info">
            <h2>{{ $tenant->business_name ?? $tenant->name }}</h2>
            @if($tenant->address_line1)
                <p>{{ $tenant->address_line1 }}</p>
                @if($tenant->address_line2)<p>{{ $tenant->address_line2 }}</p>@endif
                <p>{{ $tenant->city }}, {{ $tenant->state }} {{ $tenant->pincode }}</p>
                @if($tenant->country && $tenant->country !== 'India')<p>{{ $tenant->country }}</p>@endif
            @endif
            @if($tenant->gstin)<p>GSTIN: {{ $tenant->gstin }}</p>@endif
            @if($tenant->email)<p>Email: {{ $tenant->email }}</p>@endif
        </div>

        <div class="payment-details">
            <h3>Payment Details</h3>
            <table>
                <tr>
                    <th>Payment ID:</th>
                    <td>{{ $payment->razorpay_payment_id }}</td>
                </tr>
                <tr>
                    <th>Subscription ID:</th>
                    <td>{{ $payment->razorpay_subscription_id }}</td>
                </tr>
                <tr>
                    <th>Payment Date:</th>
                    <td>{{ $payment->payment_date->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <th>Plan:</th>
                    <td>{{ ucfirst($payment->plan) }}</td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>{{ ucfirst($payment->status) }}</td>
                </tr>
                <tr class="total">
                    <th>Amount Paid:</th>
                    <td>₹{{ number_format($payment->amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="payment-details">
            <h3>Plan Details</h3>
            <table>
                <tr>
                    <th>Plan Name:</th>
                    <td>{{ $plan['name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Billing Cycle:</th>
                    <td>Monthly</td>
                </tr>
                @if(isset($plan['invoices_per_month']))
                <tr>
                    <th>Invoices per Month:</th>
                    <td>{{ $plan['invoices_per_month'] === -1 ? 'Unlimited' : $plan['invoices_per_month'] }}</td>
                </tr>
                @endif
                @if(isset($plan['brands']))
                <tr>
                    <th>Brands:</th>
                    <td>{{ $plan['brands'] === -1 ? 'Unlimited' : $plan['brands'] }}</td>
                </tr>
                @endif
                @if(isset($plan['team_members']))
                <tr>
                    <th>Team Members:</th>
                    <td>{{ $plan['team_members'] === -1 ? 'Unlimited' : $plan['team_members'] }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="footer">
            <p>This is a computer-generated receipt. Generated on {{ $generated_at->format('F d, Y \a\t h:i A') }}</p>
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>