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
        // Try to get settings from database first, then config, then env
        try {
            $stored = \App\Models\SystemSetting::allSettings();
            $this->apiKey = $stored['whatify_api_key'] ?? config('services.whatify.api_key', env('WHATIFY_API_KEY'));
            $this->baseUrl = rtrim($stored['whatify_base_url'] ?? config('services.whatify.base_url', env('WHATIFY_BASE_URL')), '/');
        } catch (\Exception $e) {
            // If database lookup fails, use config/env
            $this->apiKey = config('services.whatify.api_key', env('WHATIFY_API_KEY'));
            $this->baseUrl = rtrim(config('services.whatify.base_url', env('WHATIFY_BASE_URL', 'https://whatify.in')), '/');
        }
        
        // Ensure base URL is set
        if (empty($this->baseUrl)) {
            $this->baseUrl = 'https://whatify.in';
        }
        
        Log::debug('WhatsApp Service Initialized', [
            'base_url' => $this->baseUrl,
            'api_key_set' => !empty($this->apiKey),
        ]);
    }

    /**
     * Send a text message
     */
    public function sendMessage(string $phone, string $message, array $options = []): array
    {
        try {
            // Format phone number to E.164 format
            $phone = $this->formatPhoneE164($phone);
            
            if (!$phone) {
                return [
                    'success' => false,
                    'error' => 'Invalid phone number format. Use E.164 format like +916868686868 or +1 (968) 082-5846',
                ];
            }
            
            // Build the full URL
            $endpoint = $this->baseUrl . '/api/send';
            
            // Build request payload
            $payload = [
                'phone' => $phone,
                'message' => $message,
            ];
            
            // Add optional parameters
            if (!empty($options['header'])) {
                $payload['header'] = $options['header'];
            }
            if (!empty($options['footer'])) {
                $payload['footer'] = $options['footer'];
            }
            if (!empty($options['buttons'])) {
                $payload['buttons'] = $options['buttons'];
            }
            
            // Log the request details for debugging
            Log::info('WhatsApp Send Request', [
                'url' => $endpoint,
                'phone' => $phone,
                'message_length' => strlen($message),
                'api_key_length' => strlen($this->apiKey),
                'api_key_first_10' => substr($this->apiKey, 0, 10) . '...',
            ]);
            
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, $payload);

            $result = $response->json();
            $statusCode = $response->status();

            Log::info('WhatsApp API Response Received', [
                'phone' => $phone,
                'status' => $statusCode,
                'response' => $result,
            ]);

            // Handle different status codes
            if ($statusCode === 200 || $statusCode === 201) {
                return [
                    'success' => true,
                    'status' => $statusCode,
                    'data' => $result,
                    'message' => 'Message sent successfully',
                ];
            }

            // Error responses
            $errorMsg = 'API Error';
            if (is_array($result) && isset($result['message'])) {
                $errorMsg = $result['message'];
            } elseif (is_array($result) && isset($result['error'])) {
                $errorMsg = $result['error'];
            }

            Log::warning('WhatsApp API Error Response', [
                'status' => $statusCode,
                'error' => $errorMsg,
                'response' => $result,
            ]);

            return [
                'success' => false,
                'status' => $statusCode,
                'error' => $errorMsg,
                'response' => $result,
                'debug' => [
                    'endpoint' => $endpoint,
                    'phone_formatted' => $phone,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception', [
                'phone' => $phone ?? 'unknown',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'debug' => [
                    'exception_code' => $e->getCode(),
                ],
            ];
        }
    }

    /**
     * Format phone number to E.164 format
     */
    protected function formatPhoneE164(string $phone): ?string
    {
        $phone = trim($phone);
        
        // Remove all whitespace and special characters except + and digits
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // Log for debugging
        Log::debug('Phone formatting', [
            'input' => $phone,
            'cleaned' => $cleaned,
            'length' => strlen($cleaned),
        ]);
        
        // If already in E.164 format (starts with +), return as-is
        if (strpos($cleaned, '+') === 0) {
            return $cleaned;
        }
        
        // If it's 10 digits (India), add country code
        if (strlen($cleaned) === 10) {
            return '+91' . $cleaned;
        }
        
        // If it's 12 digits starting with 91, add +
        if (strlen($cleaned) === 12 && strpos($cleaned, '91') === 0) {
            return '+' . $cleaned;
        }
        
        // If it's 11 digits starting with 1 (US), add +
        if (strlen($cleaned) === 11 && strpos($cleaned, '1') === 0) {
            return '+' . $cleaned;
        }
        
        // If longer and has country code prefix, add + if missing
        if (strlen($cleaned) > 10 && strlen($cleaned) < 20) {
            return '+' . $cleaned;
        }
        
        return null; // Invalid format
    }

    /**
     * Send a media message
     */
    public function sendMedia(string $phone, string $caption = '', string $mediaUrl, string $mediaType = 'image', string $fileName = ''): array
    {
        try {
            $phone = $this->formatPhoneE164($phone);
            if (!$phone) {
                return [
                    'success' => false,
                    'error' => 'Invalid phone number format.',
                ];
            }

            $endpoint = $this->baseUrl . '/api/send/media';
            
            // Build payload in exact order as Whatify API expects
            $payload = [
                'phone' => $phone,
                'media_type' => $mediaType, // image, video, document
                'media_url' => $mediaUrl,
                'caption' => $caption ?? '',
                'file_name' => $fileName ?? '',
            ];

            // Log the request being sent
            Log::debug('WhatsApp media request', [
                'endpoint' => $endpoint,
                'payload' => $payload,
                'media_url' => $mediaUrl,
            ]);

            $response = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, $payload);

            $statusCode = $response->status();
            
            // Capture raw response body first
            $rawBody = $response->body();
            
            // Try to parse as JSON
            $result = null;
            try {
                $result = $response->json();
            } catch (\Exception $e) {
                $result = $rawBody;
            }

            // Log full response for debugging
            Log::debug('WhatsApp media response', [
                'status' => $statusCode,
                'response_body' => $result,
            ]);

            if ($statusCode === 200 || $statusCode === 201) {
                return [
                    'success' => true,
                    'data' => $result,
                ];
            }

            // Extract error message
            $errorMessage = 'Media send failed';
            if (is_array($result) && isset($result['message'])) {
                $errorMessage = $result['message'];
            } elseif (is_string($result)) {
                $errorMessage = $result;
            }

            // Log error response in detail
            Log::error('WhatsApp media send failed', [
                'status' => $statusCode,
                'error_message' => $errorMessage,
                'media_url' => $mediaUrl,
                'full_response' => $result,
            ]);

            return [
                'success' => false,
                'status' => $statusCode,
                'error' => $errorMessage,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp media failed', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a template message
     */
    public function sendTemplate(string $phone, string $templateName, array $components = [], string $languageCode = 'en'): array
    {
        try {
            $phone = $this->formatPhoneE164($phone);
            if (!$phone) {
                return [
                    'success' => false,
                    'error' => 'Invalid phone number format.',
                ];
            }

            $endpoint = $this->baseUrl . '/api/send/template';
            
            $payload = [
                'phone' => $phone,
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => $languageCode],
                    'components' => $components,
                ],
            ];

            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, $payload);

            $statusCode = $response->status();
            $result = $response->json();

            if ($statusCode === 200 || $statusCode === 201) {
                return [
                    'success' => true,
                    'data' => $result,
                ];
            }

            return [
                'success' => false,
                'status' => $statusCode,
                'error' => $result['message'] ?? 'Template send failed',
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
        $businessName = $tenant->business_name ?: $tenant->name;

        $message = "🧾 *Invoice from {$businessName}*\n\n"
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

    /**
     * Send invoice PDF as media
     */
    public function sendInvoicePDF($invoice, string $phone, string $message = null): array
    {
        try {
            $tenant = $invoice->tenant;
            
            // Generate PDF URL - must be publicly accessible
            $pdfPath = "invoices/{$invoice->tenant_id}/{$invoice->invoice_number}.pdf";
            
            // Get the storage URL
            $storageUrl = \Storage::disk('public')->url($pdfPath);
            
            // Build absolute URL
            if (strpos($storageUrl, 'http') === 0) {
                // Already absolute
                $mediaUrl = $storageUrl;
            } else {
                // Relative path - make it absolute using APP_URL
                $appUrl = config('app.url');
                if (!$appUrl) {
                    throw new \Exception('APP_URL not configured');
                }
                
                // Ensure APP_URL has protocol
                if (strpos($appUrl, 'http') !== 0) {
                    $appUrl = 'http://' . $appUrl;
                }
                
                // Remove trailing slashes and combine
                $appUrl = rtrim($appUrl, '/');
                $storagePath = ltrim($storageUrl, '/');
                $mediaUrl = $appUrl . '/' . $storagePath;
            }
            
            // Validate URL is publicly accessible (not localhost or 127.0.0.1)
            if (
                strpos($mediaUrl, '127.0.0.1') !== false || 
                strpos($mediaUrl, 'localhost') !== false ||
                strpos($mediaUrl, '::1') !== false
            ) {
                Log::warning('Invoice PDF URL is not publicly accessible', [
                    'url' => $mediaUrl,
                    'invoice_id' => $invoice->id,
                    'note' => 'Use ngrok or public domain for WhatsApp media sending'
                ]);
            }
            
            Log::debug('Invoice PDF URL generated', [
                'path' => $pdfPath,
                'storage_url' => $storageUrl,
                'final_url' => $mediaUrl,
            ]);
            
            // Use default message if not provided
            if (!$message) {
                $symbol = $invoice->currency_symbol;
                $businessName = $tenant->business_name ?? $tenant->name;
                $message = "🧾 Invoice from {$businessName}\n\n"
                    . "Invoice: {$invoice->invoice_number}\n"
                    . "Amount: {$symbol}" . number_format($invoice->total_amount, 2) . "\n"
                    . "Due Date: {$invoice->due_date->format('d M Y')}";
            }
            
            // Call sendMedia with correct parameters
            $result = $this->sendMedia(
                $phone,
                $message,  // caption
                $mediaUrl, // media_url
                'document', // media_type
                $invoice->invoice_number . '.pdf' // file_name
            );
            
            Log::info('Invoice PDF sent via WhatsApp', [
                'invoice_id' => $invoice->id,
                'phone' => $phone,
                'media_url' => $mediaUrl,
                'success' => $result['success'],
            ]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send invoice PDF via WhatsApp', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'success' => false,
                'error' => 'Failed to send invoice PDF: ' . $e->getMessage(),
            ];
        }
    }


    /**
     * Send payment reminder using template message
     */
    public function sendReminderTemplate($invoice, string $phone, string $languageCode = 'en'): array
    {
        try {
            $template = $invoice->isOverdue() ? 'payment_reminder_overdue' : 'payment_reminder_pending';
            
            $daysOverdue = $invoice->isOverdue() 
                ? now()->diffInDays($invoice->due_date) 
                : $invoice->due_date->diffInDays(now());
            
            $symbol = $invoice->currency_symbol;
            $amountDue = number_format($invoice->amount_due, 2);
            
            // Template body components
            $components = [
                [
                    'type' => 'body',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => $invoice->invoice_number,
                        ],
                        [
                            'type' => 'text',
                            'text' => "{$symbol}{$amountDue}",
                        ],
                        [
                            'type' => 'text',
                            'text' => $daysOverdue . ' days',
                        ],
                    ],
                ],
            ];
            
            // Add action button if payment link exists
            if ($invoice->payment_link_token) {
                $components[] = [
                    'type' => 'button',
                    'sub_type' => 'url',
                    'index' => 0,
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => base64_encode('token=' . $invoice->payment_link_token),
                        ],
                    ],
                ];
            }
            
            $result = $this->sendTemplate($phone, $template, $components, $languageCode);
            
            Log::info('Payment reminder template sent via WhatsApp', [
                'invoice_id' => $invoice->id,
                'phone' => $phone,
                'template' => $template,
                'success' => $result['success'],
            ]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send reminder template via WhatsApp', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'Failed to send reminder template: ' . $e->getMessage(),
            ];
        }
    }
}