<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class ResetTenantMonthlyCounters extends Command
{
    protected $signature = 'tenants:reset-monthly-counters {--dry-run}';
    protected $description = 'Reset monthly invoice counters for tenants';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $today = now()->toDateString();
        $nextResetDate = now()->startOfMonth()->addMonth()->toDateString();

        $tenants = Tenant::where(function ($q) use ($today) {
                $q->whereNull('invoice_count_reset_at')
                  ->orWhereDate('invoice_count_reset_at', '<=', $today);
            })
            ->get();

        if ($dry) {
            $this->info("[DRY RUN] Would reset: {$tenants->count()} tenants.");
            return self::SUCCESS;
        }

        foreach ($tenants as $t) {
            $t->update([
                'monthly_invoice_count' => 0,
                'invoice_count_reset_at' => $nextResetDate,
            ]);
        }

        $this->info("Reset {$tenants->count()} tenants. Next reset at: {$nextResetDate}");
        return self::SUCCESS;
    }
}