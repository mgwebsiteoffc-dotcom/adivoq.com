<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppChatbotConfig;
use App\Services\WhatsAppChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected WhatsAppChatbotService $chatbotService;

    public function __construct(WhatsAppChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Verify webhook with Whatify
     * GET /whatsapp/webhook
     */
    public function verify(Request $request)
    {
        $token = $request->query('token');
        $challenge = $request->query('challenge');

        if (!$token) {
            return response('Unauthorized', 401);
        }

        $config = WhatsAppChatbotConfig::findByWebhookToken($token);
        if (!$config) {
            return response('Unauthorized', 401);
        }

        // Respond with challenge for verification
        return response($challenge, 200);
    }

    /**
     * Handle incoming messages from Whatify webhook
     * POST /whatsapp/webhook
     */
    public function handle(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return response()->json(['error' => 'No token'], 401);
        }

        $config = WhatsAppChatbotConfig::findByWebhookToken($token);
        if (!$config) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        try {
            $payload = $request->json()->all();

            // Handle different webhook types from Whatify
            if (isset($payload['type']) && $payload['type'] === 'message') {
                if (isset($payload['from']) && isset($payload['text'])) {
                    $this->chatbotService->handleIncomingMessage(
                        $config,
                        $payload['from'],
                        $payload['text']
                    );
                }
            }

            // Handle status updates (delivery, read, etc.)
            if (isset($payload['type']) && $payload['type'] === 'status') {
                $this->handleStatusUpdate($config, $payload);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error: ' . $e->getMessage(), [
                'token' => $token,
                'payload' => $request->json()->all(),
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Update message status (delivered, read, failed, etc.)
     */
    protected function handleStatusUpdate(WhatsAppChatbotConfig $config, array $payload): void
    {
        if (isset($payload['message_id']) && isset($payload['status'])) {
            $config->messages()
                ->where('external_id', $payload['message_id'])
                ->update(['status' => $payload['status']]);
        }
    }
}
