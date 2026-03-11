<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\SubscriptionPayment;
use App\Services\RazorpayPlatformService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SubscriptionService
{
    public function __construct(
        private RazorpayPlatformService $razorpay
    ) {}

    /**
     * Change tenant plan with proper handling for upgrades/downgrades
     */
    public function changePlan(Tenant $tenant, string $newPlan): bool
    {
        $currentPlan = $tenant->plan;
        $plans = config('invoicehero.plans');

        \Log::info('SubscriptionService changePlan called', [
            'tenant_id' => $tenant->id,
            'current_plan' => $currentPlan,
            'new_plan' => $newPlan,
            'plans_config' => isset($plans[$newPlan])
        ]);

        if (!isset($plans[$newPlan])) {
            throw new \InvalidArgumentException("Invalid plan: {$newPlan}");
        }

        // If no active subscription, this means user is on free plan - create new subscription
        $subscription = TenantSubscription::where('tenant_id', $tenant->id)->latest()->first();

        \Log::info('Subscription check', [
            'subscription_exists' => $subscription ? true : false,
            'subscription_status' => $subscription?->status,
            'is_active' => $subscription && in_array($subscription->status, ['active', 'authenticated'])
        ]);

        if (!$subscription || !in_array($subscription->status, ['active', 'authenticated'])) {
            // For free plan users upgrading to paid plan, this should not happen via changePlan
            // They should use the createSubscription flow instead
            \Log::warning('No active subscription found for plan change', [
                'tenant_id' => $tenant->id,
                'current_plan' => $currentPlan
            ]);
            return false; // Should use createSubscription instead
        }

        $currentPrice = $plans[$currentPlan]['price'] ?? 0;
        $newPrice = $plans[$newPlan]['price'];

        \Log::info('Plan change details', [
            'current_price' => $currentPrice,
            'new_price' => $newPrice,
            'is_upgrade' => $newPrice > $currentPrice
        ]);

        DB::beginTransaction();
        try {
            if ($newPrice > $currentPrice) {
                // UPGRADE - Apply immediately
                $this->applyImmediatePlanChange($tenant, $subscription, $newPlan);
            } else {
                // DOWNGRADE - Schedule for cycle end
                $this->schedulePlanChangeForCycleEnd($tenant, $newPlan);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Apply plan upgrade immediately
     */
    private function applyImmediatePlanChange(Tenant $tenant, TenantSubscription $subscription, string $newPlan): void
    {
        \Log::info('Applying immediate plan change', [
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription->id,
            'razorpay_subscription_id' => $subscription->razorpay_subscription_id,
            'new_plan' => $newPlan,
            'razorpay_plan_id' => $this->razorpay->planId($newPlan)
        ]);

        // Update Razorpay subscription plan
        $api = $this->razorpay->api();
        $razorpayPlanId = $this->razorpay->planId($newPlan);

        if (!$razorpayPlanId) {
            throw new \Exception("Razorpay plan ID not configured for plan: {$newPlan}");
        }

        try {
            $api->subscription->fetch($subscription->razorpay_subscription_id)->update([
                'plan_id' => $razorpayPlanId,
                'prorate' => true, // Enable proration for upgrades
            ]);

            \Log::info('Razorpay subscription updated successfully');
        } catch (\Exception $e) {
            \Log::error('Razorpay API error during plan change', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscription->razorpay_subscription_id,
                'new_plan_id' => $razorpayPlanId
            ]);
            throw $e;
        }

        // Update local records
        $subscription->update(['plan' => $newPlan]);
        $tenant->update([
            'plan' => $newPlan,
            'pending_plan' => null,
            'pending_plan_effective_at' => null,
        ]);

        // Log activity
        $tenant->owner?->logActivity('plan_upgraded', "Plan upgraded to {$newPlan}", [
            'from_plan' => $tenant->plan,
            'to_plan' => $newPlan,
        ]);
    }

    /**
     * Schedule plan downgrade for cycle end
     */
    private function schedulePlanChangeForCycleEnd(Tenant $tenant, string $newPlan): void
    {
        $effectiveAt = $tenant->subscription_ends_at;

        // If no subscription end date, use current subscription's end date or default to 30 days
        if (!$effectiveAt) {
            $subscription = TenantSubscription::where('tenant_id', $tenant->id)->latest()->first();
            if ($subscription && $subscription->current_end_at) {
                $effectiveAt = $subscription->current_end_at;
            } else {
                // Fallback: schedule for 30 days from now
                $effectiveAt = now()->addDays(30);
            }
        }

        \Log::info('Scheduling plan change for cycle end', [
            'tenant_id' => $tenant->id,
            'new_plan' => $newPlan,
            'effective_at' => $effectiveAt
        ]);

        $tenant->update([
            'pending_plan' => $newPlan,
            'pending_plan_effective_at' => $effectiveAt,
        ]);

        // Log activity
        $tenant->owner?->logActivity('plan_downgrade_scheduled', "Plan downgrade to {$newPlan} scheduled", [
            'from_plan' => $tenant->plan,
            'to_plan' => $newPlan,
            'effective_at' => $effectiveAt,
        ]);
    }

    /**
     * Apply pending plan changes (called by command or webhook)
     */
    public function applyPendingPlanChanges(): int
    {
        $tenants = Tenant::whereNotNull('pending_plan')
            ->where('pending_plan_effective_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($tenants as $tenant) {
            DB::beginTransaction();
            try {
                $subscription = TenantSubscription::where('tenant_id', $tenant->id)->latest()->first();

                if ($subscription) {
                    // Update Razorpay subscription
                    $api = $this->razorpay->api();
                    $api->subscription->fetch($subscription->razorpay_subscription_id)->update([
                        'plan_id' => $this->razorpay->planId($tenant->pending_plan),
                    ]);

                    // Update local records
                    $subscription->update(['plan' => $tenant->pending_plan]);
                }

                $tenant->update([
                    'plan' => $tenant->pending_plan,
                    'pending_plan' => null,
                    'pending_plan_effective_at' => null,
                ]);

                // Log activity
                $tenant->owner?->logActivity('plan_changed', "Plan changed to {$tenant->plan}", [
                    'from_plan' => $tenant->getOriginal('plan'),
                    'to_plan' => $tenant->plan,
                ]);

                DB::commit();
                $count++;
            } catch (\Exception $e) {
                DB::rollBack();
                // Log error but continue with other tenants
                \Log::error("Failed to apply pending plan change for tenant {$tenant->id}: " . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Generate receipt for subscription payment
     */
    public function generateReceipt(SubscriptionPayment $payment): string
    {
        $tenant = $payment->tenant;
        $subscription = $payment->subscription;

        $data = [
            'tenant' => $tenant,
            'payment' => $payment,
            'subscription' => $subscription,
            'plan' => config('invoicehero.plans.' . $payment->plan, []),
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('tenant.billing.receipt', $data);
        return $pdf->download("subscription-receipt-{$payment->razorpay_payment_id}.pdf");
    }

    /**
     * Record subscription payment from webhook
     */
    public function recordPayment(array $webhookData): SubscriptionPayment
    {
        $subscriptionId = $webhookData['subscription_id'];
        $paymentId = $webhookData['payment_id'];

        $subscription = TenantSubscription::where('razorpay_subscription_id', $subscriptionId)->first();
        if (!$subscription) {
            throw new \Exception("Subscription not found: {$subscriptionId}");
        }

        return SubscriptionPayment::create([
            'tenant_id' => $subscription->tenant_id,
            'tenant_subscription_id' => $subscription->id,
            'razorpay_payment_id' => $paymentId,
            'razorpay_subscription_id' => $subscriptionId,
            'amount' => $webhookData['amount'] / 100, // Razorpay sends in paisa
            'currency' => $webhookData['currency'] ?? 'INR',
            'status' => 'captured',
            'payment_date' => Carbon::createFromTimestamp($webhookData['created_at']),
            'plan' => $subscription->plan,
            'raw' => $webhookData,
        ]);
    }

    /**
     * Cancel pending plan change
     */
    public function cancelPendingPlanChange(Tenant $tenant): bool
    {
        if (!$tenant->pending_plan) {
            return false;
        }

        $tenant->update([
            'pending_plan' => null,
            'pending_plan_effective_at' => null,
        ]);

        $tenant->owner?->logActivity('plan_change_cancelled', 'Pending plan change cancelled');

        return true;
    }
}