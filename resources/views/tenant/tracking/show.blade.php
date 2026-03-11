@extends('layouts.tenant')
@section('title', 'Tracking Key Details')
@section('page_title', $key->name)

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('dashboard.tracking.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Keys
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Key Info Card -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tracking Key Details</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Tracking Key</label>
                    <div class="flex items-center gap-2 mt-1">
                        <code class="flex-1 bg-gray-50 px-3 py-2 rounded font-mono text-sm break-all">{{ $key->key }}</code>
                        <button onclick="copyToClipboard('{{ $key->key }}')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded font-medium">
                            Copy
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                        <p class="mt-1 inline-block px-3 py-1 text-sm font-semibold rounded-full
                            {{ $key->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $key->is_active ? 'Active' : 'Inactive' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Type</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ ucfirst($key->type) }}</p>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Monthly Limit</label>
                    <p class="mt-1 text-gray-900 font-medium">
                        {{ number_format($key->monthly_limit) }} events
                    </p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Webhook URL (for JSON)</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="text" readonly value="{{ route('track.event') }}" class="flex-1 bg-gray-50 px-3 py-2 rounded font-mono text-sm" />
                        <button onclick="copyToClipboard('{{ route('track.event') }}')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded font-medium">
                            Copy
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Include <code class="bg-gray-100 px-1 py-0.5 rounded">?key={{ $key->key }}</code> in the URL</p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Pixel URL</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="text" readonly value="{{ route('track.pixel') }}?key={{ $key->key }}" class="flex-1 bg-gray-50 px-3 py-2 rounded font-mono text-sm" />
                        <button onclick="copyToClipboard('{{ route('track.pixel') }}?key={{ $key->key }}')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded font-medium">
                            Copy
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Embed as <code class="bg-gray-100 px-1 py-0.5 rounded">&lt;img src="..." /&gt;</code></p>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500">
                        Created: {{ $key->created_at->format('M d, Y H:i') }}<br>
                        Updated: {{ $key->updated_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Statistics</h3>
            
            <div class="space-y-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_events'] ?? 0 }}</div>
                    <p class="text-sm text-gray-600 mt-1">Total Events</p>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['unique_sessions'] ?? 0 }}</div>
                        <p class="text-sm text-gray-600 mt-1">Unique Sessions</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-900">
                            {{ $stats['monthly_remaining'] ?? $key->monthly_limit }} / {{ $key->monthly_limit }}
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Remaining This Month</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-indigo-600 h-2 rounded-full" 
                                 style="width: {{ ($stats['monthly_remaining'] ?? $key->monthly_limit) / $key->monthly_limit * 100 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <a href="{{ route('dashboard.tracking.exportEvents', $key) }}" class="block w-full text-center px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-sm font-medium rounded-lg">
                        <i class="fas fa-download mr-2"></i>Export Events (CSV)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Recent Events</h3>
        </div>
        
        @if($key->events()->latest()->limit(10)->get()->isEmpty())
            <div class="px-6 py-12 text-center text-gray-500">
                <p>No events recorded yet</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Event Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Session ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Recorded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($key->events()->latest()->limit(10)->get() as $event)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $event->event_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                    {{ substr($event->event_data['session_id'] ?? '-', 0, 20) }}...
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $event->event_data['ip'] ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $event->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied!');
    });
}
</script>
@endsection
