@extends('layouts.admin')
@section('title', 'Blog Categories')
@section('page_title', 'Blog Categories')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $categories->total() }} categories</p>
    <a href="{{ route('admin.blog-categories.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
        <i class="fas fa-plus mr-1"></i>Add Category
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Name</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Slug</th>
                <th class="text-center px-4 py-3 font-semibold text-gray-600">Posts</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $cat->name }}</td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $cat->slug }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $cat->posts_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.blog-categories.edit', $cat) }}" class="text-blue-600 hover:text-blue-800 text-xs mr-2">Edit</a>
                        <form method="POST" action="{{ route('admin.blog-categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">No categories.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $categories->links() }}</div>
</div>
@endsection