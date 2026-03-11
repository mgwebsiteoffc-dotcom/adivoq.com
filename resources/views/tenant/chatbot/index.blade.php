@extends('layouts.tenant')
@section('title', 'WhatsApp Chatbots')
@section('page_title', 'WhatsApp Chatbot Management')

@section('content')
<div class="max-w-6xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">WhatsApp Chatbots</h2>
            <p class="text-sm text-gray-500 mt-1">Automate customer conversations with intelligent chatbots</p>
        </div>
        <a href="{{ route('dashboard.chatbot.create') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i>New Chatbot
        </a>
    </div>

    @if($chatbots->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <i class="fas fa-comments text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-600 font-medium">No chatbots yet</p>
            <p class="text-sm text-gray-400 mt-1">Create a WhatsApp chatbot to start automating customer conversations</p>
            <a href="{{ route('dashboard.chatbot.create') }}" class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                Create First Chatbot
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($chatbots as $chatbot)
                <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $chatbot->name }}</h3>
                            <p class="text-sm text-gray-600">Business: {{ $chatbot->business_phone ?? 'Not configured' }}</p>
                        </div>
                        <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full
                            {{ $chatbot->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $chatbot->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="space-y-3 mb-4 pb-4 border-b border-gray-100">
                        <div class="text-sm">
                            <span class="text-gray-600">Auto-replies:</span>
                            <span class="font-bold text-gray-900">
                                {{ count($chatbot->auto_replies ?? []) }}
                            </span>
                        </div>
                        @if($chatbot->auto_replies)
                            <div class="text-xs text-gray-600">
                                @foreach(array_slice($chatbot->auto_replies, 0, 2) as $reply)
                                    <div class="bg-gray-50 px-2 py-1 rounded">
                                        <strong>{{ $reply['keyword'] }}:</strong> {{ substr($reply['reply'], 0, 40) }}...
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard.chatbot.show', $chatbot) }}" class="flex-1 px-3 py-2 text-center text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg">
                            View
                        </a>
                        <a href="{{ route('dashboard.chatbot.edit', $chatbot) }}" class="flex-1 px-3 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">
                            Configure
                        </a>
                        <form method="POST" action="{{ route('dashboard.chatbot.destroy', $chatbot) }}" class="inline" onsubmit="return confirm('Delete this chatbot?')">
                            @csrf @method('DELETE')
                            <button class="flex-1 px-3 py-2 text-center text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
