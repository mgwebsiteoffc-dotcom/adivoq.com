<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecurringInvoice;
use App\Models\Invoice;

class GenerateRecurringInvoices extends Command
{
    protected $signature = 'recurring:generate';
    protected $description = 'Generate recurring invoices';

    public function handle()
    {
        $recurring = RecurringInvoice::where('active', true)
            ->whereDate('next_run_date', '<=', now())
            ->get();

        foreach ($recurring as $item) {

            Invoice::create([
                'brand_id' => $item->brand_id,
                'invoice_number' => 'INV-' . now()->format('Y') . '-' . rand(1000, 9999),
                'issue_date' => now(),
                'due_date' => now()->addDays(15),
                'subtotal' => $item->amount,
                'taxable_amount' => $item->amount,
                'total_amount' => $item->amount,
                'balance_due' => $item->amount,
                'currency' => 'INR',
                'status' => 'sent',
            ]);

            // Update next run date
            if ($item->frequency === 'monthly') {
                $item->next_run_date = now()->addMonth();
            } else {
                $item->next_run_date = now()->addMonths(3);
            }

            $item->save();
        }

        $this->info('Recurring invoices generated.');
    }
}