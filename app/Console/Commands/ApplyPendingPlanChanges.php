<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class ApplyPendingPlanChanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:apply-pending-changes {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply pending plan changes that are due';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN: Would apply pending plan changes...');
            return;
        }

        $count = $subscriptionService->applyPendingPlanChanges();

        $this->info("Applied pending plan changes for {$count} tenants.");
    }
}
