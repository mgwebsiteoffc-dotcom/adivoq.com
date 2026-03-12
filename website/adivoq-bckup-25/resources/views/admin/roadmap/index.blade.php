<!-- resources/views/admin/roadmap/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Roadmap')
@section('page_title', 'Roadmap Items')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-surface-400 text-sm">Manage product roadmap items visible to users.</p>
        <a href="{{ route('admin.roadmap.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Item
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-700/50">
                    <tr class="text-left text-xs font-semibold text-surface-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Priority</th>
                        <th class="px-6 py-4">Target</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700">
                    @forelse($items as $item)
                    @php
                        $statusColors = [
                            'planned' => 'bg-surface-500/10 text-surface-400',
                            'in_progress' => 'bg-amber-500/10 text-amber-400',
                            'completed' => 'bg-emerald-500/10 text-emerald-400',
                            'cancelled' => 'bg-red-500/10 text-red-400',
                        ];
                        $priorityColors = [
                            'low' => 'bg-surface-500/10 text-surface-400',
                            'medium' => 'bg-blue-500/10 text-blue-400',
                            'high' => 'bg-amber-500/10 text-amber-400',
                            'critical' => 'bg-red-500/10 text-red-400',
                        ];
                        $categoryColors = [
                            'feature' => 'bg-brand-500/10 text-brand-400',
                            'improvement' => 'bg-blue-500/10 text-blue-400',
                            'integration' => 'bg-emerald-500/10 text-emerald-400',
                            'bug_fix' => 'bg-red-500/10 text-red-400',
                        ];
                    @endphp
                    <tr class="hover:bg-surface-700/30">
                        <td class="px-6 py-4">
                            <p class="font-medium text-white">{{ $item->title }}</p>
                            <p class="text-surface-500 text-xs line-clamp-1">{{ Str::limit($item->description, 60) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $categoryColors[$item->category] ?? 'bg-surface-500/10 text-surface-400' }}">
                                {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$item->status] ?? '' }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$item->priority] ?? '' }}">
                                {{ ucfirst($item->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm">
                            {{ $item->target_quarter ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.roadmap.edit', $item) }}" class="p-1.5 text-surface-400 hover:text-white transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.roadmap.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-surface-400 hover:text-red-400 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            <p>No roadmap items yet</p>
                            <a href="{{ route('admin.roadmap.create') }}" class="text-brand-400 hover:text-brand-300 text-sm mt-2 inline-block">Add your first item →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($items->hasPages())
    <div class="flex justify-center">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection