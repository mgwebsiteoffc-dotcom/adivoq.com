<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentLinkController extends Controller
{
    public function show(string $token, RazorpayService $razorpay)
    {
        $invoice = Invoice::where('payment_link_token', $token)
            ->where('payment_link_expires_at', '>', now())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->with(['tenant', 'brand', 'items'])
            ->firstOrFail();

        // Mark viewed
        if (!$invoice->viewed_at && $invoice->status === 'sent') {
            $invoice->update(['viewed_at' => now(), 'status' => 'viewed']);
            $invoice->logActivity('viewed', 'Invoice viewed via payment link');
        }

        $tenant = $invoice->tenant;
        $gatewaySetting = $tenant->paymentGatewaySetting;
        $bankDetails = $tenant->primaryBank ?? $tenant->bankDetails()->first();

        $razorpayKeyId = '';
        if ($gatewaySetting?->razorpay_enabled) {
            $razorpayKeyId = $razorpay->getKeyId($tenant->id);
        }

        return view('public.payment-link', compact(
            'invoice', 'tenant', 'gatewaySetting', 'bankDetails', 'razorpayKeyId'
        ));
    }

    /**
     * Existing manual submission flow (bank transfer / UPI reference)
     */
    public function process(Request $request, string $token)
    {
        $invoice = Invoice::where('payment_link_token', $token)
            ->where('payment_link_expires_at', '>', now())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->firstOrFail();

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $invoice->amount_due,
            'payment_method' => 'required|in:bank_transfer,upi',
            'transaction_reference' => 'required|string|max:255',
        ]);

        $payment = Payment::create([
            'tenant_id' => $invoice->tenant_id,
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_date' => now()->toDateString(),
            'payment_method' => $request->payment_method,
            'transaction_reference' => $request->transaction_reference,
            'status' => 'pending',
            'notes' => 'Payment via payment link (manual submission)',
        ]);

        $invoice->logActivity('payment_submitted', "Manual payment submitted ₹{$request->amount}", [
            'payment_id' => $payment->id,
            'reference' => $request->transaction_reference,
        ]);

        return back()->with('success', 'Payment submitted! The creator will confirm your payment shortly.');
    }

    /**
     * ✅ Razorpay: Create Order (AJAX)
     */
public function razorpayCreateOrder(Request $request, string $token, \App\Services\RazorpayService $razorpay)
{
    $invoice = Invoice::where('payment_link_token', $token)
        ->where('payment_link_expires_at', '>', now())
        ->whereNotIn('status', ['paid', 'cancelled'])
        ->with(['tenant', 'brand'])
        ->firstOrFail();

    $gateway = $invoice->tenant->paymentGatewaySetting;
    if (!$gateway?->razorpay_enabled) {
        return response()->json(['success' => false, 'message' => 'Razorpay not enabled.'], 422);
    }

    $request->validate([
        'amount' => 'required|numeric|min:1|max:' . $invoice->amount_due,
    ]);

    $amount = (float) $request->amount;

    $api = $razorpay->apiForTenant($invoice->tenant_id);

    $order = $api->order->create([
        'receipt' => $invoice->invoice_number,
        'amount' => (int) round($amount * 100),
        'currency' => $invoice->currency ?? 'INR',
        'notes' => [
            'invoice_id' => (string) $invoice->id,
            'tenant_id' => (string) $invoice->tenant_id,
            'invoice_number' => (string) $invoice->invoice_number,
        ],
    ]);

    // ✅ Create a pending payment entry now (for webhook mapping)
    $payment = Payment::create([
        'tenant_id' => $invoice->tenant_id,
        'invoice_id' => $invoice->id,
        'amount' => $amount,
        'payment_date' => now()->toDateString(),
        'payment_method' => 'razorpay',
        'transaction_reference' => null,
        'gateway_order_id' => $order['id'],
        'status' => 'pending',
        'notes' => 'Razorpay order created',
    ]);

    $invoice->logActivity('razorpay_order_created', 'Razorpay order created', [
        'order_id' => $order['id'],
        'payment_id' => $payment->id,
        'amount' => $amount,
    ]);

    return response()->json([
        'success' => true,
        'order_id' => $order['id'],
        'amount' => $order['amount'],
        'currency' => $order['currency'],
        'key_id' => $razorpay->keyIdForTenant($invoice->tenant_id),
        'redirect_success' => route('payment.link.result', [$token, 'status' => 'success']),
        'redirect_failed' => route('payment.link.result', [$token, 'status' => 'failed']),
    ]);
}
    /**
     * ✅ Razorpay: Verify Payment (AJAX)
     */
public function razorpayVerifyPayment(Request $request, string $token, \App\Services\RazorpayService $razorpay)
{
    $invoice = Invoice::where('payment_link_token', $token)
        ->where('payment_link_expires_at', '>', now())
        ->whereNotIn('status', ['paid', 'cancelled'])
        ->with(['tenant', 'brand'])
        ->firstOrFail();

    $request->validate([
        'razorpay_payment_id' => 'required|string|max:255',
        'razorpay_order_id' => 'required|string|max:255',
        'razorpay_signature' => 'required|string|max:255',
    ]);

    $api = $razorpay->apiForTenant($invoice->tenant_id);

    // ✅ Verify signature
    $api->utility->verifyPaymentSignature([
        'razorpay_payment_id' => $request->razorpay_payment_id,
        'razorpay_order_id' => $request->razorpay_order_id,
        'razorpay_signature' => $request->razorpay_signature,
    ]);

    DB::beginTransaction();

    // Find pending payment created at order time
    $payment = Payment::where('tenant_id', $invoice->tenant_id)
        ->where('invoice_id', $invoice->id)
        ->where('gateway_order_id', $request->razorpay_order_id)
        ->latest()
        ->lockForUpdate()
        ->first();

    if (!$payment) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Payment order not found.'], 422);
    }

    // Idempotent
    if ($payment->status === 'confirmed') {
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Payment already confirmed.']);
    }

    $payment->update([
        'gateway_payment_id' => $request->razorpay_payment_id,
        'gateway_signature' => $request->razorpay_signature,
        'transaction_reference' => $request->razorpay_payment_id,
        'status' => 'confirmed',
        'confirmed_at' => now(),
        'notes' => 'Razorpay payment verified',
    ]);

    // Update invoice amounts/status
    $invoice->recordPayment((float) $payment->amount);

    $invoice->logActivity('razorpay_payment_verified', 'Razorpay payment verified and recorded', [
        'payment_id' => $payment->id,
        'gateway_payment_id' => $request->razorpay_payment_id,
        'gateway_order_id' => $request->razorpay_order_id,
    ]);

    DB::commit();

    return response()->json([
        'success' => true,
        'message' => 'Payment successful. Thank you!',
        'redirect' => route('payment.link.result', [$token, 'status' => 'success']),
    ]);
}

    public function result(string $token, Request $request)
{
    $invoice = Invoice::where('payment_link_token', $token)
        ->where('payment_link_expires_at', '>', now())
        ->with(['tenant', 'brand', 'items'])
        ->firstOrFail();

    $status = $request->get('status'); // success|failed
    $message = $request->get('message');

    return view('public.payment-result', compact('invoice', 'status', 'message'));
}
}