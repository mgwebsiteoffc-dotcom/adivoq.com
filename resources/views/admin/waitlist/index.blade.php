@extends('layouts.admin')
@section('title', 'Waitlist')
@section('page_title', 'Waitlist Management')

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total', 'value' => $stats['total'], 'color' => 'bg-gray-500'],
        ['label' => 'Waiting', 'value' => $stats['waiting'], 'color' => 'bg-yellow-500'],
        ['label' => 'Invited', 'value' => $stats['invited'], 'color' => 'bg-blue-500'],
        ['label' => 'Converted', 'value' => $stats['converted'], 'color' => 'bg-green-500'],
    ] as $s)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase">{{ $s['label'] }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $s['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex items-end gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search email..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-48">
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">All</option>
            <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
            <option value="invited" {{ request('status') === 'invited' ? 'selected' : '' }}>Invited</option>
            <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">Filter</button>
    </form>
    <a href="{{ route('admin.waitlist.export') }}" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
        <i class="fas fa-download mr-1"></i>Export CSV
    </a>
</div>

<form method="POST" action="{{ route('admin.waitlist.invite') }}" id="inviteForm">
    @csrf
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left"><input type="checkbox" id="selectAll" class="rounded"></th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Email</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Name</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Type</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Signed Up</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($entries as $entry)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if($entry->status === 'waiting')
                                <input type="checkbox" name="ids[]" value="{{ $entry->id }}" class="rounded entry-checkbox">
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $entry->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $entry->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 capitalize">{{ $entry->creator_type }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $entry->status === 'waiting' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $entry->status === 'invited' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $entry->status === 'converted' ? 'bg-green-100 text-green-700' : '' }}">
                                {{ ucfirst($entry->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $entry->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">No entries.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <i class="fas fa-paper-plane mr-1"></i>Mark Selected as Invited
            </button>
            <div>{{ $entries->links() }}</div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.entry-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
@endsection