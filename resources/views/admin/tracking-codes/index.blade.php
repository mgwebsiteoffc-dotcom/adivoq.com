@extends('layouts.admin')
@section('title', 'Tracking Codes & Analytics')
@section('page_title', 'Tracking Codes')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-gray-600">Manage Meta Pixel, Google Analytics, Clarity, and custom tracking scripts</p>
    </div>
    <a href="{{ route('admin.tracking-codes.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <i class="fas fa-plus mr-2"></i>Add Tracking Code
    </a>
</div>

@if (session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs font-semibold text-gray-500 uppercase">Total Events</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format(\App\Models\TrackedEvent::count()) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs font-semibold text-gray-500 uppercase">Today's Events</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format(\App\Models\TrackedEvent::whereDate('created_at', today())->count()) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs font-semibold text-gray-500 uppercase">Active Codes</p>
        <p class="text-2xl font-bold text-indigo-600 mt-2">{{ \App\Models\TrackingCode::where('is_enabled', true)->count() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs font-semibold text-gray-500 uppercase">Total Codes</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ \App\Models\TrackingCode::count() }}</p>
    </div>
</div>

<!-- Tracking Codes Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Service</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Tracking ID / Code</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Created By</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Created</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($codes as $code)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            @if ($code->service_name === 'meta_pixel')
                                <i class="fab fa-facebook text-blue-600"></i>
                                <span class="font-medium">Meta Pixel</span>
                            @elseif ($code->service_name === 'google_analytics')
                                <i class="fab fa-google text-red-600"></i>
                                <span class="font-medium">Google Analytics</span>
                            @elseif ($code->service_name === 'clarity')
                                <i class="fas fa-chart-line text-cyan-600"></i>
                                <span class="font-medium">Clarity</span>
                            @else
                                <i class="fas fa-code text-purple-600"></i>
                                <span class="font-medium">Custom</span>
                            @endif
                        </div>
                        @if ($code->display_name)
                            <p class="text-xs text-gray-500 mt-1">{{ $code->display_name }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if ($code->tracking_id)
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded font-mono">{{ $code->tracking_id }}</code>
                        @else
                            <span class="text-xs text-gray-500">Custom Script</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   {{ $code->is_enabled ? 'checked' : '' }}
                                   onchange="toggleTracking({{ $code->id }})"
                                   class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-4 peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                            <span class="ml-2 text-xs font-medium">{{ $code->is_enabled ? 'Enabled' : 'Disabled' }}</span>
                        </label>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        {{ $code->adminUser?->name ?? 'System' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        {{ $code->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.tracking-codes.edit', $code) }}" 
                               class="text-indigo-600 hover:text-indigo-700 text-xs font-medium">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.tracking-codes.destroy', $code) }}" 
                                  class="inline" onsubmit="return confirm('Delete this tracking code?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2 block opacity-30"></i>
                        <p class="text-sm">No tracking codes configured yet</p>
                        <a href="{{ route('admin.tracking-codes.create') }}" class="text-indigo-600 text-sm font-medium mt-2 inline-block">
                            Add your first tracking code
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $codes->links() }}
</div>

<script>
function toggleTracking(codeId) {
    fetch(`/admin/tracking-codes/${codeId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    }).catch(error => {
        console.error('Error:', error);
        alert('Failed to toggle tracking code');
    });
}
</script>
@endsection
