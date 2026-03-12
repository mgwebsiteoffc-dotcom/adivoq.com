<!-- resources/views/admin/blog/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Blog Posts')
@section('page_title', 'Blog Posts')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-surface-400 text-sm">Manage blog posts and articles.</p>
        <a href="{{ route('admin.blog.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Post
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-4">
        <form action="{{ route('admin.blog.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search posts..."
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm">
            </div>
            <div class="w-full sm:w-40">
                <select name="status" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-surface-100 focus:border-brand-500 text-sm">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="w-full sm:w-40">
                <select name="category" class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-surface-100 focus:border-brand-500 text-sm">
                    <option value="">All Categories</option>
                    <option value="tax-tips" {{ request('category') === 'tax-tips' ? 'selected' : '' }}>Tax Tips</option>
                    <option value="guides" {{ request('category') === 'guides' ? 'selected' : '' }}>Guides</option>
                    <option value="news" {{ request('category') === 'news' ? 'selected' : '' }}>News</option>
                    <option value="tutorials" {{ request('category') === 'tutorials' ? 'selected' : '' }}>Tutorials</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-surface-700 text-white font-medium rounded-lg hover:bg-surface-600 transition-colors text-sm">
                Filter
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-700/50">
                    <tr class="text-left text-xs font-semibold text-surface-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Post</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Views</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700">
                    @forelse($posts as $post)
                    <tr class="hover:bg-surface-700/30">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($post->cover_image)
                                <img src="{{ asset('uploads/blog/' . $post->cover_image) }}" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                <div class="w-12 h-12 bg-surface-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-white line-clamp-1">{{ $post->title }}</p>
                                    <p class="text-surface-500 text-xs">{{ Str::limit($post->excerpt, 50) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-500/10 text-brand-400">
                                {{ ucfirst(str_replace('-', ' ', $post->category)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $post->status === 'published' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-surface-500/10 text-surface-400' }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm">
                            {{ number_format($post->views_count) }}
                        </td>
                        <td class="px-6 py-4 text-surface-400 text-sm">
                            {{ $post->published_at?->format('M d, Y') ?? 'Not published' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @if($post->status === 'published')
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="p-1.5 text-surface-400 hover:text-white transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                @endif
                                <a href="{{ route('admin.blog.edit', $post) }}" class="p-1.5 text-surface-400 hover:text-white transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Delete this post?')">
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
                        <td colspan="6" class="px-6 py-12 text-center text-surface-500">No blog posts found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($posts->hasPages())
    <div class="flex justify-center">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection