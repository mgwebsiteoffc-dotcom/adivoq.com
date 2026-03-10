@extends('layouts.admin')
@section('title', 'Roadmap')
@section('page_title', 'Roadmap Management')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex items-end gap-3">
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">All Status</option>
            @foreach(['planned','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <select name="category" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">All Categories</option>
            @foreach(['feature','improvement','bug_fix','integration'] as $c)
                <option value="{{ $c }}" {{ request('category') === $c ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$c)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">Filter</button>
    </form>
    <a href="{{ route('admin.roadmap.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <i class="fas fa-plus mr-1"></i>New Item
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Title</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Category</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Priority</th>
                <th class="text-center px-4 py-3 font-semibold text-gray-600">Votes</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Target</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->title }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $item->category === 'feature' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $item->category === 'improvement' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $item->category === 'bug_fix' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $item->category === 'integration' ? 'bg-teal-100 text-teal-700' : '' }}">
                            {{ ucfirst(str_replace('_',' ',$item->category)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $item->status === 'planned' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $item->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $item->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $item->status === 'cancelled' ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ ucfirst(str_replace('_',' ',$item->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $item->priority === 'high' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $item->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $item->priority === 'low' ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ ucfirst($item->priority) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center font-medium text-gray-600">{{ $item->votes_count }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $item->target_quarter ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.roadmap.edit', $item) }}" class="text-blue-600 hover:text-blue-800 text-xs mr-2">Edit</a>
                        <form method="POST" action="{{ route('admin.roadmap.destroy', $item) }}" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">No roadmap items.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $items->links() }}</div>
</div>
@endsection