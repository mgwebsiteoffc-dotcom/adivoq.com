@extends('layouts.admin')
@section('title', 'Guides')
@section('page_title', 'Guide Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex items-end gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-48">
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">All</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">Filter</button>
    </form>
    <a href="{{ route('admin.guides.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <i class="fas fa-plus mr-1"></i>New Guide
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Title</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Category</th>
                <th class="text-center px-4 py-3 font-semibold text-gray-600">Steps</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($guides as $guide)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ Str::limit($guide->title, 50) }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $guide->category ?? '—' }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $guide->steps_count }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $guide->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ ucfirst($guide->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.guides.edit', $guide) }}" class="text-blue-600 hover:text-blue-800 text-xs mr-2">Edit & Steps</a>
                        <form method="POST" action="{{ route('admin.guides.destroy', $guide) }}" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">No guides yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $guides->links() }}</div>
</div>
@endsection