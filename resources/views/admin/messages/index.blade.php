@extends('layouts.admin')
@section('title', 'Messages')
@section('page_title', 'Contact Messages')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <form method="GET" class="flex items-end gap-3">
            <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread ({{ $unreadCount }})</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">Filter</button>
        </form>
    </div>
    @if($unreadCount > 0)
        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">{{ $unreadCount }} unread</span>
    @endif
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">From</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Subject</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Received</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($messages as $msg)
                <tr class="hover:bg-gray-50 {{ $msg->status === 'unread' ? 'bg-blue-50/50' : '' }}">
                    <td class="px-4 py-3">
                        <div>
                            <p class="font-medium text-gray-900 {{ $msg->status === 'unread' ? 'font-bold' : '' }}">{{ $msg->name }}</p>
                            <p class="text-xs text-gray-500">{{ $msg->email }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $msg->subject ?? Str::limit($msg->message, 50) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $msg->status === 'unread' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $msg->status === 'read' ? 'bg-gray-100 text-gray-600' : '' }}
                            {{ $msg->status === 'replied' ? 'bg-green-100 text-green-700' : '' }}">
                            {{ ucfirst($msg->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $msg->created_at->diffForHumans() }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.messages.show', $msg) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">No messages.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $messages->links() }}</div>
</div>
@endsection