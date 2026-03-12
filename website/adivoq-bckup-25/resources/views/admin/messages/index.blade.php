<!-- resources/views/admin/messages/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Messages')
@section('page_title', 'Contact Messages')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <p class="text-surface-400 text-sm">Messages from the contact form.</p>
        <span class="inline-flex items-center px-3 py-1 bg-surface-700 text-surface-300 text-sm rounded-lg">
            {{ $messages->total() }} total messages
        </span>
    </div>

    {{-- Messages List --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <div class="divide-y divide-surface-700">
            @forelse($messages as $message)
            <a href="{{ route('admin.messages.show', $message) }}" class="block p-5 hover:bg-surface-700/30 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <p class="font-medium text-white">{{ $message->name }}</p>
                            @if($message->status === 'new')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-brand-500/10 text-brand-400">
                                New
                            </span>
                            @endif
                        </div>
                        <p class="text-surface-500 text-sm">{{ $message->email }}</p>
                        @if($message->subject)
                        <p class="text-surface-400 text-sm mt-1 font-medium">{{ $message->subject }}</p>
                        @endif
                        <p class="text-surface-400 text-sm mt-1 line-clamp-2">{{ $message->message }}</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-surface-500 text-xs">{{ $message->created_at->format('M d, Y') }}</p>
                        <p class="text-surface-600 text-xs">{{ $message->created_at->format('h:i A') }}</p>
                    </div>
                </div>
            </a>
            @empty
            <div class="p-12 text-center text-surface-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <p>No messages yet</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($messages->hasPages())
    <div class="flex justify-center">
        {{ $messages->links() }}
    </div>
    @endif
</div>
@endsection