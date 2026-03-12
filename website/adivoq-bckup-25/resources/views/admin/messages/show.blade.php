<!-- resources/views/admin/messages/show.blade.php -->
@extends('layouts.admin')

@section('title', 'View Message')
@section('page_title', 'View Message')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center gap-2 text-surface-400 hover:text-white text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Messages
        </a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        {{-- Header --}}
        <div class="p-6 border-b border-surface-700">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-white">{{ $message->name }}</h2>
                    <a href="mailto:{{ $message->email }}" class="text-brand-400 hover:text-brand-300 text-sm">{{ $message->email }}</a>
                </div>
                <div class="text-right">
                    <p class="text-surface-400 text-sm">{{ $message->created_at->format('M d, Y') }}</p>
                    <p class="text-surface-500 text-xs">{{ $message->created_at->format('h:i A') }}</p>
                </div>
            </div>
            @if($message->subject)
            <div class="mt-4">
                <span class="text-surface-500 text-sm">Subject:</span>
                <p class="text-white font-medium">{{ $message->subject }}</p>
            </div>
            @endif
        </div>

        {{-- Message Body --}}
        <div class="p-6">
            <div class="bg-surface-700/30 rounded-lg p-4">
                <p class="text-surface-300 whitespace-pre-wrap leading-relaxed">{{ $message->message }}</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="p-6 border-t border-surface-700 flex items-center justify-between">
            <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject ?? 'Your message to AdivoQ' }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Reply via Email
            </a>
            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Delete this message?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-red-400 hover:text-red-300 text-sm">
                    Delete Message
                </button>
            </form>
        </div>
    </div>
</div>
@endsection