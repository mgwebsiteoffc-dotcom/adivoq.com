<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    public function generate(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load(['tenant', 'brand', 'items', 'payments']);

        $tenant = $invoice->tenant;
        $bankDetails = $tenant->primaryBank ?? $tenant->bankDetails()->first();
        $invoiceSettings = $tenant->invoiceSetting;

        $data = [
            'invoice' => $invoice,
            'tenant' => $tenant,
            'brand' => $invoice->brand,
            'items' => $invoice->items,
            'payments' => $invoice->payments()->confirmed()->get(),
            'bankDetails' => $bankDetails,
            'invoiceSettings' => $invoiceSettings,
            'currencySymbol' => $invoice->currency_symbol,
        ];

        $template = $invoiceSettings?->template ?? 'default';

        return Pdf::loadView("pdf.invoice-{$template}", $data)
            ->setPaper('a4')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);
    }

    public function download(Invoice $invoice)
    {
        $pdf = $this->generate($invoice);
        $filename = "Invoice-{$invoice->invoice_number}.pdf";
        return $pdf->download($filename);
    }

    public function stream(Invoice $invoice)
    {
        $pdf = $this->generate($invoice);
        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function save(Invoice $invoice): string
    {
        $pdf = $this->generate($invoice);
        $path = "invoices/{$invoice->tenant_id}/{$invoice->invoice_number}.pdf";
        \Storage::disk('public')->put($path, $pdf->output());
        return $path;
    }
}