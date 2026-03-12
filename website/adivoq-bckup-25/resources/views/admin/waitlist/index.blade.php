<!-- resources/views/admin/waitlist/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Waitlist')
@section('page_title', 'Waitlist')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-surface-400 text-sm">People who signed up for early access.</p>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1 bg-surface-700 text-surface-300 text-sm rounded-lg">
                {{ $entries->total() }} total entries
            </span>
            <button onclick="exportWaitlist()" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-700 text-white font-medium rounded-lg hover:bg-surface-600 transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-700/50">
                    <tr class="text-left text-xs font-semibold text-surface-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Creator Type</th>
                        <th class="px-6 py-4">Source</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700">
                    @forelse($entries as $entry)
                    <tr class="hover:bg-surface-700/30">
                        <td class="px-6 py-4 text-white font-medium">{{ $entry->name }}</td>
                        <td class="px-6 py-4">
                            <a href="mailto:{{ $entry->email }}" class="text-brand-400 hover:text-brand-300 text-sm">{{ $entry->email }}</a>
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm">{{ ucfirst($entry->creator_type ?? '-') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-surface-500/10 text-surface-400">
                                {{ ucfirst($entry->source) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'waiting' => 'bg-amber-500/10 text-amber-400',
                                    'invited' => 'bg-blue-500/10 text-blue-400',
                                    'converted' => 'bg-emerald-500/10 text-emerald-400',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$entry->status] ?? 'bg-surface-500/10 text-surface-400' }}">
                                {{ ucfirst($entry->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm">{{ $entry->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="mailto:{{ $entry->email }}" class="p-1.5 text-surface-400 hover:text-white transition-colors" title="Send Email">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </a>
                                <form action="{{ route('admin.waitlist.destroy', $entry) }}" method="POST" class="inline" onsubmit="return confirm('Remove this entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-surface-400 hover:text-red-400 transition-colors" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-surface-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p>No waitlist entries yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($entries->hasPages())
    <div class="flex justify-center">
        {{ $entries->links() }}
    </div>
    @endif
</div>

<script>
function exportWaitlist() {
    // Simple CSV export
    const rows = [
        ['Name', 'Email', 'Creator Type', 'Source', 'Status', 'Joined Date']
    ];
    
    document.querySelectorAll('tbody tr').forEach(row => {
        const cols = row.querySelectorAll('td');
        if (cols.length > 0) {
            rows.push([
                cols[0].innerText.trim(),
                cols[1].innerText.trim(),
                cols[2].innerText.trim(),
                cols[3].innerText.trim(),
                cols[4].innerText.trim(),
                cols[5].innerText.trim()
            ]);
        }
    });
    
    const csvContent = rows.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'waitlist_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
}
</script>
@endsection