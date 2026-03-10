@extends('layouts.admin')
@section('title', $item ? 'Edit Roadmap Item' : 'New Roadmap Item')
@section('page_title', $item ? 'Edit Roadmap Item' : 'Add Roadmap Item')

@section('content')
<div class="max-w-xl">
    <a href="{{ route('admin.roadmap.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i>Back</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ $item ? route('admin.roadmap.update', $item) : route('admin.roadmap.store') }}">
            @csrf
            @if($item) @method('PUT') @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $item?->title) }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description', $item?->description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach(['feature','improvement','bug_fix','integration'] as $c)
                                <option value="{{ $c }}" {{ old('category', $item?->category) === $c ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$c)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach(['planned','in_progress','completed','cancelled'] as $s)
                                <option value="{{ $s }}" {{ old('status', $item?->status) === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Priority *</label>
                        <select name="priority" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach(['low','medium','high'] as $p)
                                <option value="{{ $p }}" {{ old('priority', $item?->priority ?? 'medium') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Target Quarter</label>
                        <input type="text" name="target_quarter" value="{{ old('target_quarter', $item?->target_quarter) }}" placeholder="e.g., Q1 2025" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100 text-right">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        {{ $item ? 'Update' : 'Create' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection