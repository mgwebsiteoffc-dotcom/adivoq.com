<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\RazorpayWebhookLog;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request, RazorpayService $razorpay)
    {
        $signature = $request->header('X-Razorpay-Signature');
        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);

        $event = $payload['event'] ?? null;

        $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
        $orderId = $paymentEntity['order_id'] ?? null;
        $paymentId = $paymentEntity['id'] ?? null;

        // If no order_id, ignore (but log)
        if (!$orderId) {
            RazorpayWebhookLog::create([
                'tenant_id' => null,
                'event' => $event,
                'signature_valid' => false,
                'status' => 'ignored',
                'message' => 'No order_id in payload',
                'payload' => $payload,
            ]);
            return response()->json(['ok' => true]);
        }

        $payment = Payment::where('gateway_order_id', $orderId)->latest()->first();

        // Can't map to tenant without payment row
        if (!$payment) {
            RazorpayWebhookLog::create([
                'tenant_id' => null,
                'event' => $event,
                'gateway_order_id' => $orderId,
                'gateway_payment_id' => $paymentId,
                'signature_valid' => false,
                'status' => 'ignored',
                'message' => 'Order not found in DB',
                'payload' => $payload,
            ]);
            return response()->json(['ok' => true]);
        }

        $tenantId = $payment->tenant_id;

        $secret = $razorpay->webhookSecretForTenant($tenantId);
        if (!$secret || !$signature) {
            RazorpayWebhookLog::create([
                'tenant_id' => $tenantId,
                'event' => $event,
                'gateway_order_id' => $orderId,
                'gateway_payment_id' => $paymentId,
                'signature_valid' => false,
                'status' => 'error',
                'message' => 'Webhook secret/signature missing',
                'payload' => $payload,
            ]);
            return response()->json(['ok' => false, 'message' => 'Webhook secret/signature missing'], 400);
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);
        $signatureValid = hash_equals($expected, $signature);

        if (!$signatureValid) {
            RazorpayWebhookLog::create([
                'tenant_id' => $tenantId,
                'event' => $event,
                'gateway_order_id' => $orderId,
                'gateway_payment_id' => $paymentId,
                'signature_valid' => false,
                'status' => 'error',
                'message' => 'Invalid signature',
                'payload' => $payload,
            ]);
            return response()->json(['ok' => false, 'message' => 'Invalid signature'], 400);
        }

        try {
            DB::beginTransaction();

            $payment = Payment::where('id', $payment->id)->lockForUpdate()->first();
            $invoice = Invoice::where('id', $payment->invoice_id)->lockForUpdate()->first();

            if (!$invoice || $invoice->status === 'cancelled') {
                RazorpayWebhookLog::create([
                    'tenant_id' => $tenantId,
                    'event' => $event,
                    'gateway_order_id' => $orderId,
                    'gateway_payment_id' => $paymentId,
                    'signature_valid' => true,
                    'status' => 'ignored',
                    'message' => 'Invoice missing or cancelled',
                    'payload' => $payload,
                ]);
                DB::rollBack();
                return response()->json(['ok' => true]);
            }

            if ($event === 'payment.captured') {
                if ($payment->status !== 'confirmed') {
                    $payment->update([
                        'gateway_payment_id' => $paymentId,
                        'transaction_reference' => $paymentId,
                        'status' => 'confirmed',
                        'confirmed_at' => now(),
                        'notes' => trim(($payment->notes ?? '') . ' | webhook captured'),
                    ]);

                    $invoice->recordPayment((float) $payment->amount);

                    $invoice->logActivity('razorpay_webhook_captured', 'Webhook: payment.captured', [
                        'gateway_order_id' => $orderId,
                        'gateway_payment_id' => $paymentId,
                        'payment_id' => $payment->id,
                    ]);
                }

                RazorpayWebhookLog::create([
                    'tenant_id' => $tenantId,
                    'event' => $event,
                    'gateway_order_id' => $orderId,
                    'gateway_payment_id' => $paymentId,
                    'signature_valid' => true,
                    'status' => 'processed',
                    'message' => 'Captured processed',
                    'payload' => $payload,
                ]);
            } elseif ($event === 'payment.failed') {
                if ($payment->status !== 'confirmed') {
                    $payment->update([
                        'gateway_payment_id' => $paymentId,
                        'transaction_reference' => $paymentId,
                        'status' => 'failed',
                        'notes' => trim(($payment->notes ?? '') . ' | webhook failed'),
                    ]);

                    $invoice->logActivity('razorpay_webhook_failed', 'Webhook: payment.failed', [
                        'gateway_order_id' => $orderId,
                        'gateway_payment_id' => $paymentId,
                        'payment_id' => $payment->id,
                    ]);
                }

                RazorpayWebhookLog::create([
                    'tenant_id' => $tenantId,
                    'event' => $event,
                    'gateway_order_id' => $orderId,
                    'gateway_payment_id' => $paymentId,
                    'signature_valid' => true,
                    'status' => 'processed',
                    'message' => 'Failed processed',
                    'payload' => $payload,
                ]);
            } else {
                RazorpayWebhookLog::create([
                    'tenant_id' => $tenantId,
                    'event' => $event,
                    'gateway_order_id' => $orderId,
                    'gateway_payment_id' => $paymentId,
                    'signature_valid' => true,
                    'status' => 'ignored',
                    'message' => 'Event ignored',
                    'payload' => $payload,
                ]);
            }

            DB::commit();
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Razorpay webhook error', ['error' => $e->getMessage()]);

            RazorpayWebhookLog::create([
                'tenant_id' => $tenantId,
                'event' => $event,
                'gateway_order_id' => $orderId,
                'gateway_payment_id' => $paymentId,
                'signature_valid' => true,
                'status' => 'error',
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json(['ok' => false], 500);
        }
    }
}