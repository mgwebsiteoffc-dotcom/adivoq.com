<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        $this->invoice->load(['brand', 'items']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this->invoice
        ]);

        return $this->subject('Invoice ' . $this->invoice->invoice_number)
            ->view('emails.invoice')
            ->attachData(
                $pdf->output(),
                $this->invoice->invoice_number . '.pdf'
            );
    }
}