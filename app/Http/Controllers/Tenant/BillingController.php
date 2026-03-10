<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantSubscription;
use App\Services\RazorpayPlatformService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;

        $subscription = TenantSubscription::where('tenant_id', $tenant->id)->latest()->first();

        $plans = config('invoicehero.plans'); // uses your existing config
        // Only paid plans here
        $paidPlans = ['starter','professional','enterprise'];

        return view('tenant.billing.index', compact('tenant','subscription','plans','paidPlans'));
    }

    /**
     * Create Razorpay subscription and return subscription_id to open checkout.js
     */
    public function createSubscription(Request $request, RazorpayPlatformService $rzp)
    {
        $tenant = auth()->user()->tenant;

        $request->validate([
            'plan' => 'required|in:starter,professional,enterprise',
        ]);

        $planKey = $request->plan;
        $razorpayPlanId = $rzp->planId($planKey);

        if (!$razorpayPlanId) {
            return response()->json(['success' => false, 'message' => 'Razorpay plan not configured.'], 422);
        }

        $api = $rzp->api();

        // Create customer if not exists
        $subscription = TenantSubscription::where('tenant_id', $tenant->id)->latest()->first();

        $customerId = $subscription?->razorpay_customer_id;

        if (!$customerId) {
            $customer = $api->customer->create([
                'name' => $tenant->business_name ?? $tenant->name,
                'email' => $tenant->email,
                'contact' => $tenant->phone,
                'notes' => ['tenant_id' => (string)$tenant->id],
            ]);
            $customerId = $customer['id'];
        }

        // Create subscription
        $sub = $api->subscription->create([
            'plan_id' => $razorpayPlanId,
            'customer_id' => $customerId,
            'total_count' => 120, // 10 years safety
            'quantity' => 1,
            'customer_notify' => 1,
            'notes' => [
                'tenant_id' => (string) $tenant->id,
                'plan' => $planKey,
            ],
        ]);

        // Save local row
        $local = TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'plan' => $planKey,
            'gateway' => 'razorpay',
            'razorpay_customer_id' => $customerId,
            'razorpay_subscription_id' => $sub['id'],
            'status' => $sub['status'] ?? 'created',
            'current_start_at' => isset($sub['current_start']) ? Carbon::createFromTimestamp($sub['current_start']) : null,
            'current_end_at' => isset($sub['current_end']) ? Carbon::createFromTimestamp($sub['current_end']) : null,
            'raw' => $sub->toArray(),
        ]);

        return response()->json([
            'success' => true,
            'key_id' => $rzp->keyId(),
            'subscription_id' => $sub['id'],
            'plan' => $planKey,
            'redirect_success' => route('dashboard.billing.result', ['status' => 'success']),
            'redirect_failed' => route('dashboard.billing.result', ['status' => 'failed']),
        ]);
    }

    /**
     * Verify subscription payment signature after checkout
     */
    public function verifySubscription(Request $request, RazorpayPlatformService $rzp)
    {
        $tenant = auth()->user()->tenant;

        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_subscription_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $api = $rzp->api();

        // Verify signature
        $api->utility->verifyPaymentSignature([
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_subscription_id' => $request->razorpay_subscription_id,
            'razorpay_signature' => $request->razorpay_signature,
        ]);

        DB::beginTransaction();

        $sub = TenantSubscription::where('tenant_id', $tenant->id)
            ->where('razorpay_subscription_id', $request->razorpay_subscription_id)
            ->latest()
            ->lockForUpdate()
            ->first();

        if (!$sub) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Subscription not found.'], 422);
        }

        // fetch latest subscription state from Razorpay
        $rzpSub = $api->subscription->fetch($request->razorpay_subscription_id);

        $sub->update([
            'status' => $rzpSub['status'] ?? 'authenticated',
            'last_payment_id' => $request->razorpay_payment_id,
            'current_start_at' => isset($rzpSub['current_start']) ? Carbon::createFromTimestamp($rzpSub['current_start']) : null,
            'current_end_at' => isset($rzpSub['current_end']) ? Carbon::createFromTimestamp($rzpSub['current_end']) : null,
            'raw' => $rzpSub->toArray(),
        ]);

        // Activate tenant plan immediately (webhook will keep it in sync)
        $tenant->update([
            'plan' => $sub->plan,
            'plan_status' => 'active',
            'subscription_ends_at' => $sub->current_end_at,
            'status' => 'active',
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'redirect' => route('dashboard.billing.result', ['status' => 'success']),
        ]);
    }

    public function result(Request $request)
    {
        $status = $request->get('status'); // success|failed
        return view('tenant.billing.result', compact('status'));
    }

    public function cancel(Request $request, RazorpayPlatformService $rzp)
    {
        $tenant = auth()->user()->tenant;
        $sub = TenantSubscription::where('tenant_id', $tenant->id)->latest()->first();

        if (!$sub || !$sub->razorpay_subscription_id) {
            return back()->with('error', 'No active subscription found.');
        }

        $api = $rzp->api();
        // cancel at end of cycle (recommended)
        $api->subscription->fetch($sub->razorpay_subscription_id)->cancel(['cancel_at_cycle_end' => 1]);

        $sub->update(['status' => 'cancelled']);

        $tenant->update([
            'plan_status' => 'cancelled',
            // keep access until subscription_ends_at
        ]);

        return back()->with('success', 'Subscription cancellation requested (at cycle end).');
    }
}