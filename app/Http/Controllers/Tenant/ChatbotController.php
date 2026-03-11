<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppChatbotConfig;
use App\Services\WhatsAppChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected WhatsAppChatbotService $chatbotService;

    public function __construct(WhatsAppChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * List chatbot configurations
     */
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $configs = $tenant->whatsappChatbots()->latest()->get();

        return view('tenant.chatbot.index', compact('configs'));
    }

    /**
     * Create new chatbot
     */
    public function create()
    {
        return view('tenant.chatbot.create');
    }

    /**
     * Store new chatbot
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|unique:whatsapp_chatbot_configs',
        ]);

        $tenant = auth()->user()->tenant;

        $config = $tenant->whatsappChatbots()->create([
            'phone_number' => $request->phone_number,
            'status' => 'active',
        ]);

        return redirect()->route('dashboard.chatbot.show', $config)
            ->with('success', 'WhatsApp chatbot created! Webhook URL: ' . route('whatsapp.webhook', ['token' => $config->webhook_token]));
    }

    /**
     * Show chatbot details
     */
    public function show(WhatsAppChatbotConfig $config)
    {
        $this->authorize('view', $config);

        $recentMessages = $config->messages()->latest()->limit(20)->get();
        $webhookUrl = route('whatsapp.webhook', ['token' => $config->webhook_token]);

        return view('tenant.chatbot.show', compact('config', 'recentMessages', 'webhookUrl'));
    }

    /**
     * Edit chatbot settings
     */
    public function edit(WhatsAppChatbotConfig $config)
    {
        $this->authorize('update', $config);

        return view('tenant.chatbot.edit', compact('config'));
    }

    /**
     * Update auto-reply rules
     */
    public function updateRules(Request $request, WhatsAppChatbotConfig $config)
    {
        $this->authorize('update', $config);

        $request->validate([
            'rules' => 'required|array',
            'rules.*.keyword' => 'required|string|max:100',
            'rules.*.response' => 'required|string|max:1000',
        ]);

        $rules = array_map(function ($rule) {
            return [
                'keyword' => $rule['keyword'],
                'response' => $rule['response'],
            ];
        }, $request->input('rules', []));

        $this->chatbotService->updateAutoReplies($config, $rules);

        return back()->with('success', 'Auto-reply rules updated!');
    }

    /**
     * Toggle status
     */
    public function toggleStatus(WhatsAppChatbotConfig $config)
    {
        $this->authorize('update', $config);

        $config->update([
            'status' => $config->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Chatbot status updated!');
    }

    /**
     * Get conversation with a contact
     */
    public function conversation(Request $request, WhatsAppChatbotConfig $config)
    {
        $this->authorize('view', $config);

        $phone = $request->query('phone');
        $messages = $this->chatbotService->getConversationHistory($config, $phone);

        return view('tenant.chatbot.conversation', compact('config', 'phone', 'messages'));
    }

    /**
     * Send manual message
     */
    public function sendMessage(Request $request, WhatsAppChatbotConfig $config)
    {
        $this->authorize('update', $config);

        $request->validate([
            'phone' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'message' => 'required|string|max:4096',
        ]);

        $success = $this->chatbotService->sendMessage(
            $config,
            $request->phone,
            $request->message
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Message sent!' : 'Failed to send message',
        ]);
    }

    /**
     * Delete chatbot
     */
    public function destroy(WhatsAppChatbotConfig $config)
    {
        $this->authorize('delete', $config);

        $config->delete();

        return redirect()->route('dashboard.chatbot.index')
            ->with('success', 'Chatbot deleted.');
    }
}
