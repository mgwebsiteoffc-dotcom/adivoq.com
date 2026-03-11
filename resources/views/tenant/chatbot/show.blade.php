@extends('layouts.tenant')
@section('title', 'Chatbot Details')
@section('page_title', $chatbot->name)

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('dashboard.chatbot.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Chatbot Info Card -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $chatbot->name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">Business Phone: <strong>{{ $chatbot->business_phone }}</strong></p>
                </div>
                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                    {{ $chatbot->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $chatbot->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div class="pt-4 border-t border-gray-200 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-600 uppercase">Webhook Configuration</label>
                    <div class="flex items-center gap-2 mt-2">
                        <input type="text" readonly 
                            value="{{ route('webhook.whatsapp') }}?token={{ $chatbot->webhook_token }}"
                            class="flex-1 bg-gray-50 px-3 py-2 rounded font-mono text-xs border border-gray-200" />
                        <button onclick="copyToClipboard('{{ route('webhook.whatsapp') }}?token={{ $chatbot->webhook_token }}')" 
                            class="px-3 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-sm rounded font-medium">
                            Copy
                        </button>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-600 uppercase">Webhook Token</label>
                    <p class="mt-2 font-mono text-sm bg-gray-50 px-3 py-2 rounded border border-gray-200">
                        {{ $chatbot->webhook_token }}
                    </p>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <a href="{{ route('dashboard.chatbot.edit', $chatbot) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-edit mr-2"></i>Edit Configuration
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Statistics</h3>
            
            <div class="space-y-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_messages'] ?? 0 }}</div>
                    <p class="text-sm text-gray-600 mt-1">Total Messages</p>
                </div>

                <div class="border-t border-gray-100 pt-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['unique_contacts'] ?? 0 }}</div>
                    <p class="text-sm text-gray-600 mt-1">Unique Contacts</p>
                </div>

                <div class="border-t border-gray-100 pt-4 text-center">
                    <div class="text-lg font-bold text-gray-900">{{ count($chatbot->auto_replies ?? []) }}</div>
                    <p class="text-sm text-gray-600 mt-1">Auto-Reply Rules</p>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-500">
                        Created: {{ $chatbot->created_at->format('M d, Y H:i') }}<br>
                        Updated: {{ $chatbot->updated_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-Reply Rules -->
    <div class="bg-white rounded-xl border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Auto-Reply Rules</h3>
        </div>
        
        @if(!$chatbot->auto_replies || empty($chatbot->auto_replies))
            <div class="px-6 py-12 text-center text-gray-500">
                <p>No auto-reply rules configured</p>
                <a href="{{ route('dashboard.chatbot.edit', $chatbot) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium mt-2 inline-block">
                    Add Rules
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($chatbot->auto_replies as $reply)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <span class="inline-block px-2.5 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                    {{ ucfirst($reply['match_type'] ?? 'contains') }}
                                </span>
                            </div>
                        </div>
                        <p class="text-sm"><strong>Keyword:</strong> <code class="bg-gray-100 px-2 py-1 rounded font-mono">{{ $reply['keyword'] }}</code></p>
                        <p class="text-sm text-gray-700 mt-2"><strong>Reply:</strong> {{ $reply['reply'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Recent Messages -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Recent Conversations</h3>
            <select id="statusFilter" onchange="location.href = '{{ route('dashboard.chatbot.show', $chatbot) }}?status=' + this.value" class="px-3 py-1.5 border border-gray-300 rounded text-sm">
                <option value="">All Messages</option>
                <option value="pending">Pending</option>
                <option value="sent">Sent</option>
                <option value="delivered">Delivered</option>
                <option value="read">Read</option>
                <option value="failed">Failed</option>
            </select>
        </div>
        
        @if($messages->isEmpty())
            <div class="px-6 py-12 text-center text-gray-500">
                <p>No messages yet</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">From</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Direction</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900 font-mono">{{ $message->contact_phone }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ substr($message->message_text, 0, 50) }}{{ strlen($message->message_text) > 50 ? '...' : '' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full
                                        {{ $message->direction === 'inbound' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($message->direction) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full
                                        {{ match($message->status) {
                                            'delivered' => 'bg-green-100 text-green-700',
                                            'read' => 'bg-blue-100 text-blue-700',
                                            'failed' => 'bg-red-100 text-red-700',
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        } }}">
                                        {{ ucfirst($message->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $message->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Webhook URL copied! Configure this in your Whatify account.');
    });
}
</script>
@endsection
