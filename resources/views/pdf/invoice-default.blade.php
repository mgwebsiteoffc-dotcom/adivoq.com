<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .invoice-container { padding: 30px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .company-info { max-width: 50%; }
        .company-name { font-size: 20px; font-weight: bold; color: {{ $invoiceSettings->invoice_color ?? '#4F46E5' }}; }
        .invoice-title { font-size: 28px; font-weight: bold; color: {{ $invoiceSettings->invoice_color ?? '#4F46E5' }}; text-align: right; }
        .invoice-number { font-size: 14px; color: #666; text-align: right; margin-top: 5px; }
        .bill-section { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .bill-to, .bill-from { width: 48%; }
        .section-label { font-size: 10px; text-transform: uppercase; font-weight: bold; color: {{ $invoiceSettings->invoice_color ?? '#4F46E5' }}; margin-bottom: 5px; letter-spacing: 1px; }
        .section-name { font-size: 14px; font-weight: bold; margin-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th { background: {{ $invoiceSettings->invoice_color ?? '#4F46E5' }}; color: white; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid #eee; }
        tbody tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-table { width: 300px; margin-left: auto; }
        .totals-table td { padding: 5px 10px; font-size: 12px; }
        .totals-table .total-row { font-size: 16px; font-weight: bold; border-top: 2px solid {{ $invoiceSettings->invoice_color ?? '#4F46E5' }}; color: {{ $invoiceSettings->invoice_color ?? '#4F46E5' }}; }
        .bank-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .bank-details h4 { color: {{ $invoiceSettings->invoice_color ?? '#4F46E5' }}; margin-bottom: 8px; font-size: 13px; }
        .notes-section { margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; }
        .notes-section h4 { font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        .notes-section p { font-size: 11px; color: #666; }
        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
        .meta-info { font-size: 11px; color: #666; }
        .meta-info table { margin-bottom: 0; }
        .meta-info td { padding: 3px 8px; border: none; }
    </style>
</head>
<body>
    <div class="invoice-container">
        {{-- Header --}}
        <table style="margin-bottom: 30px;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: none;">
                    @if($invoiceSettings?->show_logo && $tenant->logo)
                        <img src="{{ public_path('storage/' . $tenant->logo) }}" style="max-height: 60px; margin-bottom: 10px;">
                    @endif
                    <div class="company-name">{{ $tenant->business_name ?? $tenant->name }}</div>
                    <div style="font-size: 11px; color: #666; margin-top: 5px;">
                        @if($tenant->address_line1){{ $tenant->address_line1 }}<br>@endif
                        @if($tenant->address_line2){{ $tenant->address_line2 }}<br>@endif
                        @if($tenant->city){{ $tenant->city }}, @endif{{ $tenant->state }} {{ $tenant->pincode }}<br>
                        @if($tenant->gstin)<strong>GSTIN:</strong> {{ $tenant->gstin }}<br>@endif
                        @if($tenant->pan_number)<strong>PAN:</strong> {{ $tenant->pan_number }}@endif
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top; border: none;">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                    <div class="meta-info" style="margin-top: 15px;">
                        <table style="margin-left: auto;">
                            <tr><td><strong>Date:</strong></td><td>{{ $invoice->issue_date->format('d M Y') }}</td></tr>
                            <tr><td><strong>Due Date:</strong></td><td>{{ $invoice->due_date->format('d M Y') }}</td></tr>
                            @if($invoice->reference_number)
                            <tr><td><strong>Ref:</strong></td><td>{{ $invoice->reference_number }}</td></tr>
                            @endif
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="status-badge {{ $invoice->isPaid() ? 'status-paid' : 'status-pending' }}">
                                        {{ strtoupper($invoice->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Bill To --}}
        <table style="margin-bottom: 25px;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <div class="section-label">Bill To</div>
                    <div class="section-name">{{ $brand->name }}</div>
                    <div style="font-size: 11px; color: #666;">
                        @if($brand->contact_person){{ $brand->contact_person }}<br>@endif
                        @if($brand->address_line1){{ $brand->address_line1 }}<br>@endif
                        @if($brand->city){{ $brand->city }}, @endif{{ $brand->state }} {{ $brand->pincode }}<br>
                        @if($brand->email){{ $brand->email }}<br>@endif
                        @if($brand->gstin)<strong>GSTIN:</strong> {{ $brand->gstin }}<br>@endif
                        @if($brand->pan_number)<strong>PAN:</strong> {{ $brand->pan_number }}@endif
                    </div>
                </td>
                <td style="width: 50%; border: none;"></td>
            </tr>
        </table>

        {{-- Items Table --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Description</th>
                    @if($items->first()?->hsn_sac_code)<th style="width: 12%;">HSN/SAC</th>@endif
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-right">Rate</th>
                    <th style="width: 18%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    @if($items->first()?->hsn_sac_code)<td>{{ $item->hsn_sac_code }}</td>@endif
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ $currencySymbol }}{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <table class="totals-table">
            <tr><td>Subtotal</td><td class="text-right">{{ $currencySymbol }}{{ number_format($invoice->subtotal, 2) }}</td></tr>
            @if($invoice->discount_amount > 0)
            <tr><td>Discount {{ $invoice->discount_type === 'percentage' ? '(' . $invoice->discount_value . '%)' : '' }}</td><td class="text-right" style="color: #dc2626;">-{{ $currencySymbol }}{{ number_format($invoice->discount_amount, 2) }}</td></tr>
            @endif
            @if($invoice->cgst_amount > 0)
            <tr><td>CGST ({{ $invoice->cgst_rate }}%)</td><td class="text-right">{{ $currencySymbol }}{{ number_format($invoice->cgst_amount, 2) }}</td></tr>
            <tr><td>SGST ({{ $invoice->sgst_rate }}%)</td><td class="text-right">{{ $currencySymbol }}{{ number_format($invoice->sgst_amount, 2) }}</td></tr>
            @endif
            @if($invoice->igst_amount > 0)
            <tr><td>IGST ({{ $invoice->igst_rate }}%)</td><td class="text-right">{{ $currencySymbol }}{{ number_format($invoice->igst_amount, 2) }}</td></tr>
            @endif
            <tr class="total-row">
                <td style="padding-top: 8px;"><strong>Total</strong></td>
                <td class="text-right" style="padding-top: 8px;"><strong>{{ $currencySymbol }}{{ number_format($invoice->total_amount, 2) }}</strong></td>
            </tr>
            @if($invoice->tds_amount > 0)
            <tr><td>TDS ({{ $invoice->tds_rate }}%)</td><td class="text-right" style="color: #dc2626;">-{{ $currencySymbol }}{{ number_format($invoice->tds_amount, 2) }}</td></tr>
            <tr><td><strong>Net Receivable</strong></td><td class="text-right"><strong>{{ $currencySymbol }}{{ number_format($invoice->net_receivable, 2) }}</strong></td></tr>
            @endif
            @if($invoice->amount_paid > 0)
            <tr><td>Amount Paid</td><td class="text-right" style="color: #059669;">-{{ $currencySymbol }}{{ number_format($invoice->amount_paid, 2) }}</td></tr>
            <tr><td><strong>Amount Due</strong></td><td class="text-right"><strong>{{ $currencySymbol }}{{ number_format($invoice->amount_due, 2) }}</strong></td></tr>
            @endif
        </table>

        {{-- Bank Details --}}
        @if($bankDetails)
        <div class="bank-details">
            <h4>Bank Details for Payment</h4>
            <table style="font-size: 11px;">
                <tr><td style="border: none; padding: 2px 8px; width: 120px;"><strong>Bank:</strong></td><td style="border: none; padding: 2px 8px;">{{ $bankDetails->bank_name }}</td></tr>
                <tr><td style="border: none; padding: 2px 8px;"><strong>Account Name:</strong></td><td style="border: none; padding: 2px 8px;">{{ $bankDetails->account_holder_name }}</td></tr>
                <tr><td style="border: none; padding: 2px 8px;"><strong>Account No:</strong></td><td style="border: none; padding: 2px 8px;">{{ $bankDetails->account_number }}</td></tr>
                <tr><td style="border: none; padding: 2px 8px;"><strong>IFSC:</strong></td><td style="border: none; padding: 2px 8px;">{{ $bankDetails->ifsc_code }}</td></tr>
                @if($bankDetails->upi_id)
                <tr><td style="border: none; padding: 2px 8px;"><strong>UPI:</strong></td><td style="border: none; padding: 2px 8px;">{{ $bankDetails->upi_id }}</td></tr>
                @endif
            </table>
        </div>
        @endif

        {{-- Notes & Terms --}}
        @if($invoice->notes)
        <div class="notes-section">
            <h4>Notes</h4>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

        @if($invoice->terms_and_conditions)
        <div class="notes-section">
            <h4>Terms & Conditions</h4>
            <p>{{ $invoice->terms_and_conditions }}</p>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            Generated by InvoiceHero | {{ $tenant->business_name ?? $tenant->name }}
        </div>
    </div>
</body>
</html>