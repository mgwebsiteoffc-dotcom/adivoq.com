<!-- resources/views/admin/roadmap/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Roadmap Item')
@section('page_title', 'Edit Roadmap Item')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.roadmap.index') }}" class="inline-flex items-center gap-2 text-surface-400 hover:text-white text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Roadmap
        </a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
        <form action="{{ route('admin.roadmap.update', $item) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-surface-300 mb-1.5">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $item->title) }}" required
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 text-sm">
                @error('title')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-surface-300 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 text-sm resize-none">{{ old('description', $item->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-surface-300 mb-1.5">Category *</label>
                    <select id="category" name="category" required
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                        <option value="feature" {{ old('category', $item->category) === 'feature' ? 'selected' : '' }}>Feature</option>
                        <option value="improvement" {{ old('category', $item->category) === 'improvement' ? 'selected' : '' }}>Improvement</option>
                        <option value="integration" {{ old('category', $item->category) === 'integration' ? 'selected' : '' }}>Integration</option>
                        <option value="bug_fix" {{ old('category', $item->category) === 'bug_fix' ? 'selected' : '' }}>Bug Fix</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-surface-300 mb-1.5">Status *</label>
                    <select id="status" name="status" required
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                        <option value="planned" {{ old('status', $item->status) === 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="in_progress" {{ old('status', $item->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $item->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $item->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="priority" class="block text-sm font-medium text-surface-300 mb-1.5">Priority *</label>
                    <select id="priority" name="priority" required
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                        <option value="low" {{ old('priority', $item->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $item->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $item->priority) === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority', $item->priority) === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div>
                    <label for="target_quarter" class="block text-sm font-medium text-surface-300 mb-1.5">Target Quarter</label>
                    <input type="text" id="target_quarter" name="target_quarter" value="{{ old('target_quarter', $item->target_quarter) }}"
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm"
                        placeholder="e.g., Q2 2025">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4">
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
                        Update Item
                    </button>
                    <a href="{{ route('admin.roadmap.index') }}" class="px-6 py-2.5 text-surface-400 hover:text-white transition-colors text-sm">
                        Cancel
                    </a>
                </div>
                <form action="{{ route('admin.roadmap.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-red-400 hover:text-red-300 text-sm">
                        Delete
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection