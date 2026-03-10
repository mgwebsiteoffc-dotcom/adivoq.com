<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.whatify.base_url', 'https://whatify.in/api');
        $this->apiKey = config('services.whatify.api_key', env('WHATIFY_API_KEY'));
    }

    /**
     * Send a text message
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/send', [
                'phone' => $phone,
                'message' => $message,
            ]);

            $result = $response->json();

            Log::info('WhatsApp message sent', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $result,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp message failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send a media message
     */
    public function sendMedia(string $phone, string $message, string $mediaUrl, string $mediaType = 'document'): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/send/media', [
                'phone' => $phone,
                'message' => $message,
                'media' => [
                    'type' => $mediaType,
                    'url' => $mediaUrl,
                ],
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp media failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a template message
     */
    public function sendTemplate(string $phone, string $templateName, array $components = [], string $languageCode = 'en'): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/send/template', [
                'phone' => $phone,
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => $languageCode],
                    'components' => $components,
                ],
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp template failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send invoice notification via WhatsApp
     */
    public function sendInvoiceNotification($invoice, string $phone): array
    {
        $tenant = $invoice->tenant;
        $brand = $invoice->brand;
        $symbol = $invoice->currency_symbol;

        $message = "🧾 *Invoice from {$tenant->business_name ?? $tenant->name}*\n\n"
            . "Invoice: *{$invoice->invoice_number}*\n"
            . "Amount: *{$symbol}" . number_format($invoice->total_amount, 2) . "*\n"
            . "Due Date: *{$invoice->due_date->format('d M Y')}*\n\n";

        if ($invoice->payment_link_token) {
            $message .= "💳 Pay online: " . route('payment.link', $invoice->payment_link_token) . "\n\n";
        }

        $message .= "Thank you for your business!";

        return $this->sendMessage($phone, $message);
    }

    /**
     * Send payment reminder via WhatsApp
     */
    public function sendPaymentReminder($invoice, string $phone): array
    {
        $symbol = $invoice->currency_symbol;
        $daysOverdue = now()->diffInDays($invoice->due_date);

        $message = "⏰ *Payment Reminder*\n\n"
            . "Invoice *{$invoice->invoice_number}* is ";

        if ($invoice->isOverdue()) {
            $message .= "overdue by {$daysOverdue} days.\n";
        } else {
            $message .= "due on {$invoice->due_date->format('d M Y')}.\n";
        }

        $message .= "Amount Due: *{$symbol}" . number_format($invoice->amount_due, 2) . "*\n\n";

        if ($invoice->payment_link_token) {
            $message .= "💳 Pay now: " . route('payment.link', $invoice->payment_link_token) . "\n\n";
        }

        $message .= "Please process the payment at the earliest.";

        return $this->sendMessage($phone, $message);
    }

    /**
     * Send payment confirmation via WhatsApp
     */
    public function sendPaymentConfirmation($payment, string $phone): array
    {
        $invoice = $payment->invoice;
        $symbol = $invoice->currency_symbol;

        $message = "✅ *Payment Received*\n\n"
            . "Invoice: *{$invoice->invoice_number}*\n"
            . "Amount Received: *{$symbol}" . number_format($payment->amount, 2) . "*\n"
            . "Payment Method: *" . ucfirst(str_replace('_', ' ', $payment->payment_method)) . "*\n"
            . "Date: *{$payment->payment_date->format('d M Y')}*\n\n";

        if ($invoice->amount_due > 0) {
            $message .= "Remaining Due: *{$symbol}" . number_format($invoice->amount_due, 2) . "*\n\n";
        } else {
            $message .= "✨ Invoice is fully paid!\n\n";
        }

        $message .= "Thank you! 🙏";

        return $this->sendMessage($phone, $message);
    }
}