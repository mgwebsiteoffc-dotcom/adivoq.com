@extends('layouts.admin')
@section('title', 'Activity Logs')
@section('page_title', 'Activity Logs')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <select name="action" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">All Actions</option>
            @foreach($actions as $a)
                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="From">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="To">
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">Filter</button>
        <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 text-gray-500 text-sm">Reset</a>
    </form>
    <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
        <i class="fas fa-download mr-1"></i>Export CSV
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Action</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Description</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Tenant</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">IP</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">{{ $log->action }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ Str::limit($log->description, 60) }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $log->tenant?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $log->ip_address }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">No logs found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $logs->links() }}</div>
</div>
@endsection