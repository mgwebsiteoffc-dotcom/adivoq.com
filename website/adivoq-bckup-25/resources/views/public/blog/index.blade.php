<!-- resources/views/public/blog/index.blade.php -->
@extends('layouts.public')

@section('title', 'Blog — AdivoQ')
@section('meta_description', 'Tips, guides, and insights for content creators on taxes, invoicing, and financial management.')

@section('content')
<div class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-16">
            <h1 class="text-3xl sm:text-4xl font-bold text-white">Creator Finance Blog</h1>
            <p class="mt-4 text-surface-400 max-w-2xl mx-auto">Tips, guides, and insights to help you manage your creator business finances like a pro.</p>
        </div>

        {{-- Posts Grid --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
            <article class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden hover:border-surface-600 transition-colors group">
                @if($post->cover_image)
                <div class="aspect-video bg-surface-700 overflow-hidden">
                    <img src="{{ asset('uploads/blog/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                @else
                <div class="aspect-video bg-gradient-to-br from-brand-600/20 to-indigo-600/20 flex items-center justify-center">
                    <svg class="w-12 h-12 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/>
                    </svg>
                </div>
                @endif

                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-600/20 text-brand-400">
                            {{ ucfirst(str_replace('-', ' ', $post->category)) }}
                        </span>
                        <span class="text-surface-500 text-xs">{{ $post->published_at?->format('M d, Y') }}</span>
                    </div>

                    <h2 class="text-lg font-semibold text-white mb-2 group-hover:text-brand-400 transition-colors">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h2>

                    <p class="text-surface-400 text-sm line-clamp-2">{{ $post->excerpt }}</p>

                    <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center gap-1 mt-4 text-sm text-brand-400 hover:text-brand-300 font-medium">
                        Read more
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </article>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-surface-500">No blog posts yet. Check back soon!</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($posts->hasPages())
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection