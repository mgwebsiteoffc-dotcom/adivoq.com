@extends('layouts.tenant')
@section('title', $chatbot ? 'Edit Chatbot' : 'Create Chatbot')
@section('page_title', $chatbot ? 'Edit: ' . $chatbot->name : 'New WhatsApp Chatbot')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('dashboard.chatbot.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">{{ $chatbot ? 'Edit Chatbot' : 'Create New Chatbot' }}</h3>
            <p class="text-sm text-gray-600 mt-1">
                {{ $chatbot ? 'Update chatbot settings and auto-reply rules' : 'Set up a new WhatsApp chatbot for automated responses' }}
            </p>
        </div>

        <form method="POST" action="{{ $chatbot ? route('dashboard.chatbot.update', $chatbot) : route('dashboard.chatbot.store') }}" class="p-6 space-y-8">
            @csrf
            @if($chatbot)
                @method('PUT')
            @endif

            <!-- Basic Info Section -->
            <div class="space-y-6">
                <h4 class="text-lg font-bold text-gray-900">Basic Information</h4>

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Chatbot Name *</label>
                    <input type="text" name="name" id="name" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="e.g., Customer Support Bot" value="{{ old('name', $chatbot?->name) }}">
                    @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="business_phone" class="block text-sm font-semibold text-gray-900 mb-2">Business Phone Number *</label>
                    <input type="text" name="business_phone" id="business_phone" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="e.g., 91XXXXXXXXXX" value="{{ old('business_phone', $chatbot?->business_phone) }}"
                        pattern="[0-9]+">
                    <p class="text-xs text-gray-500 mt-1">Include country code (e.g., 91 for India)</p>
                    @error('business_phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="is_active" class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                            {{ old('is_active', $chatbot?->is_active ?? true) ? 'checked' : '' }}
                            class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-gray-900">Active</span>
                    </label>
                </div>
            </div>

            <!-- Webhook Configuration Section -->
            <div class="space-y-6 pt-6 border-t border-gray-200">
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Webhook Configuration</h4>
                    <p class="text-sm text-gray-600 mt-1">Configure this webhook in your Whatify account to receive incoming messages</p>
                </div>

                @if($chatbot)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-2">
                        <div>
                            <label class="text-xs font-semibold text-blue-900 uppercase">Webhook URL</label>
                            <div class="flex items-center gap-2 mt-1">
                                <input type="text" readonly 
                                    value="{{ route('webhook.whatsapp') }}?token={{ $chatbot->webhook_token }}"
                                    class="flex-1 bg-white px-3 py-2 rounded font-mono text-sm border border-blue-200" />
                                <button type="button" onclick="copyToClipboard('{{ route('webhook.whatsapp') }}?token={{ $chatbot->webhook_token }}')"
                                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded font-medium">
                                    Copy
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-blue-900 uppercase">Webhook Token</label>
                            <p class="text-sm font-mono mt-1">{{ $chatbot->webhook_token }}</p>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center text-gray-600">
                        <p class="text-sm">Webhook configuration will be available after creating the chatbot</p>
                    </div>
                @endif
            </div>

            <!-- Auto-Reply Rules Section -->
            <div class="space-y-6 pt-6 border-t border-gray-200">
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Auto-Reply Rules</h4>
                    <p class="text-sm text-gray-600 mt-1">Configure keyword-based automatic replies</p>
                </div>

                <div id="autoRepliesContainer" class="space-y-4">
                    @if($chatbot && $chatbot->auto_replies)
                        @foreach($chatbot->auto_replies as $index => $reply)
                            <div class="auto-reply-item border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <input type="hidden" name="auto_replies[{{ $index }}][index]" value="{{ $index }}">
                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-900 uppercase mb-1">Keyword</label>
                                        <input type="text" name="auto_replies[{{ $index }}][keyword]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            placeholder="e.g., hello" value="{{ $reply['keyword'] }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-900 uppercase mb-1">Match Type</label>
                                        <select name="auto_replies[{{ $index }}][match_type]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <option value="contains" {{ ($reply['match_type'] ?? 'contains') === 'contains' ? 'selected' : '' }}>Contains</option>
                                            <option value="exact" {{ ($reply['match_type'] ?? 'exact') === 'exact' ? 'selected' : '' }}>Exact Match</option>
                                            <option value="starts" {{ ($reply['match_type'] ?? 'starts') === 'starts' ? 'selected' : '' }}>Starts With</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-900 uppercase mb-1">Reply Message</label>
                                    <textarea name="auto_replies[{{ $index }}][reply]" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                                        rows="3" placeholder="Enter the auto-reply message" required>{{ $reply['reply'] }}</textarea>
                                </div>
                                <button type="button" onclick="removeAutoReply(this)" class="mt-3 text-red-600 text-sm font-medium hover:text-red-700">
                                    <i class="fas fa-trash mr-1"></i>Remove Rule
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>

                <button type="button" onclick="addAutoReply()" class="px-4 py-2 border-2 border-indigo-600 text-indigo-600 font-medium rounded-lg hover:bg-indigo-50">
                    <i class="fas fa-plus mr-2"></i>Add Auto-Reply Rule
                </button>
            </div>

            <div class="pt-6 border-t border-gray-200 flex gap-3 justify-end">
                <a href="{{ route('dashboard.chatbot.index') }}" 
                    class="px-5 py-2.5 text-gray-700 font-medium border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                    {{ $chatbot ? 'Save Changes' : 'Create Chatbot' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let autoReplyCounter = {{ $chatbot && $chatbot->auto_replies ? count($chatbot->auto_replies) : 0 }};

function addAutoReply() {
    const container = document.getElementById('autoRepliesContainer');
    const newItem = document.createElement('div');
    newItem.className = 'auto-reply-item border border-gray-200 rounded-lg p-4 bg-gray-50';
    newItem.innerHTML = `
        <input type="hidden" name="auto_replies[${autoReplyCounter}][index]" value="${autoReplyCounter}">
        <div class="grid grid-cols-2 gap-4 mb-3">
            <div>
                <label class="block text-xs font-semibold text-gray-900 uppercase mb-1">Keyword</label>
                <input type="text" name="auto_replies[${autoReplyCounter}][keyword]" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="e.g., hello" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-900 uppercase mb-1">Match Type</label>
                <select name="auto_replies[${autoReplyCounter}][match_type]" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="contains">Contains</option>
                    <option value="exact">Exact Match</option>
                    <option value="starts">Starts With</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-900 uppercase mb-1">Reply Message</label>
            <textarea name="auto_replies[${autoReplyCounter}][reply]" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                rows="3" placeholder="Enter the auto-reply message" required></textarea>
        </div>
        <button type="button" onclick="removeAutoReply(this)" class="mt-3 text-red-600 text-sm font-medium hover:text-red-700">
            <i class="fas fa-trash mr-1"></i>Remove Rule
        </button>
    `;
    container.appendChild(newItem);
    autoReplyCounter++;
}

function removeAutoReply(button) {
    button.closest('.auto-reply-item').remove();
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Webhook URL copied!');
    });
}
</script>
@endsection
