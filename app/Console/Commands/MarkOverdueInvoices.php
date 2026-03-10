<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class MarkOverdueInvoices extends Command
{
    protected $signature = 'invoices:mark-overdue {--dry-run}';
    protected $description = 'Mark unpaid invoices as overdue when due_date has passed';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $query = Invoice::query()
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereIn('status', ['sent', 'viewed', 'partially_paid']);

        $count = $query->count();

        if ($dry) {
            $this->info("[DRY RUN] Would mark {$count} invoices as overdue.");
            return self::SUCCESS;
        }

        $updated = $query->update(['status' => 'overdue']);

        $this->info("Marked {$updated} invoices as overdue.");
        return self::SUCCESS;
    }
}