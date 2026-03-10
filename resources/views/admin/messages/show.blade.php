@extends('layouts.admin')
@section('title', 'Message from ' . $message->name)
@section('page_title', 'View Message')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.messages.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i>Back</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ $message->name }}</h3>
                <p class="text-sm text-gray-500">{{ $message->email }} • {{ $message->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <span class="px-3 py-1 text-xs font-medium rounded-full
                {{ $message->status === 'unread' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $message->status === 'read' ? 'bg-gray-100 text-gray-600' : '' }}
                {{ $message->status === 'replied' ? 'bg-green-100 text-green-700' : '' }}">
                {{ ucfirst($message->status) }}
            </span>
        </div>

        @if($message->subject)
            <p class="text-sm font-semibold text-gray-700 mb-3">Subject: {{ $message->subject }}</p>
        @endif

        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">
            {!! nl2br(e($message->message)) !!}
        </div>

        <div class="mt-4">
            <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject ?? 'Your message to InvoiceHero' }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                <i class="fas fa-reply mr-2"></i>Reply via Email
            </a>
        </div>
    </div>

    {{-- Admin Notes & Status --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h4 class="text-sm font-bold text-gray-900 mb-3">Admin Notes & Status</h4>
        <form method="POST" action="{{ route('admin.messages.update', $message) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="read" {{ $message->status === 'read' ? 'selected' : '' }}>Read</option>
                        <option value="replied" {{ $message->status === 'replied' ? 'selected' : '' }}>Replied</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Admin Notes</label>
                    <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ $message->admin_notes }}</textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection