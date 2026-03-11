<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\TenantSubscriptionEvent;
use App\Models\SubscriptionPayment;
use App\Services\RazorpayPlatformService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RazorpayPlatformWebhookController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function handle(Request $request, RazorpayPlatformService $rzp)
    {
        $signature = $request->header('X-Razorpay-Signature');
        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);

        $secret = $rzp->webhookSecret();
        if (!$secret || !$signature) {
            TenantSubscriptionEvent::create([
                'status' => 'error',
                'message' => 'Webhook secret/signature missing',
                'payload' => $payload,
            ]);
            return response()->json(['ok' => false], 400);
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);
        $valid = hash_equals($expected, $signature);

        $event = $payload['event'] ?? null;

        $subEntity = $payload['payload']['subscription']['entity'] ?? null;
        $subId = $subEntity['id'] ?? null;

        $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
        $paymentId = $paymentEntity['id'] ?? null;

        if (!$valid) {
            TenantSubscriptionEvent::create([
                'event' => $event,
                'razorpay_subscription_id' => $subId,
                'razorpay_payment_id' => $paymentId,
                'signature_valid' => false,
                'status' => 'error',
                'message' => 'Invalid signature',
                'payload' => $payload,
            ]);
            return response()->json(['ok' => false], 400);
        }

        if (!$subId) {
            TenantSubscriptionEvent::create([
                'event' => $event,
                'signature_valid' => true,
                'status' => 'ignored',
                'message' => 'No subscription id',
                'payload' => $payload,
            ]);
            return response()->json(['ok' => true]);
        }

        $local = TenantSubscription::where('razorpay_subscription_id', $subId)->latest()->first();
        $tenantId = $local?->tenant_id;

        try {
            DB::beginTransaction();

            if ($local) {
                $local->update([
                    'status' => $subEntity['status'] ?? $local->status,
                    'current_start_at' => isset($subEntity['current_start']) ? Carbon::createFromTimestamp($subEntity['current_start']) : $local->current_start_at,
                    'current_end_at' => isset($subEntity['current_end']) ? Carbon::createFromTimestamp($subEntity['current_end']) : $local->current_end_at,
                    'last_payment_id' => $paymentId ?: $local->last_payment_id,
                    'raw' => $subEntity,
                ]);

                $tenant = Tenant::find($local->tenant_id);
                if ($tenant) {
                    // Sync plan status based on subscription status
                    $status = $subEntity['status'] ?? null;

                    if (in_array($status, ['active', 'authenticated'])) {
                        $tenant->update([
                            'plan' => $local->plan,
                            'plan_status' => 'active',
                            'subscription_ends_at' => $local->current_end_at,
                            'status' => 'active',
                        ]);

                        // Apply any pending plan changes when subscription becomes active
                        if ($tenant->pending_plan) {
                            $this->subscriptionService->applyPendingPlanChanges();
                        }
                    } elseif (in_array($status, ['cancelled', 'completed', 'expired', 'halted'])) {
                    }
                }
            }

            // Record subscription payment if this is a payment event
            if ($paymentEntity && in_array($event, ['payment.captured', 'payment.failed'])) {
                try {
                    $this->subscriptionService->recordPayment($paymentEntity);
                } catch (\Exception $e) {
                    // Log but don't fail the webhook
                    \Log::error("Failed to record subscription payment: " . $e->getMessage(), [
                        'payment_id' => $paymentId,
                        'subscription_id' => $subId,
                    ]);
                }
            }

            TenantSubscriptionEvent::create([
                'tenant_id' => $tenantId,
                'event' => $event,
                'razorpay_subscription_id' => $subId,
                'razorpay_payment_id' => $paymentId,
                'signature_valid' => true,
                'status' => 'processed',
                'message' => 'Processed',
                'payload' => $payload,
            ]);

            DB::commit();
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            DB::rollBack();

            TenantSubscriptionEvent::create([
                'tenant_id' => $tenantId,
                'event' => $event,
                'razorpay_subscription_id' => $subId,
                'razorpay_payment_id' => $paymentId,
                'signature_valid' => true,
                'status' => 'error',
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json(['ok' => false], 500);
        }
    }
}