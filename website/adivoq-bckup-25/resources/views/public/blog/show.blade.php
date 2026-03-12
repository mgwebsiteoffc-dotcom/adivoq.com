<!-- resources/views/public/blog/show.blade.php -->
@extends('layouts.public')

@section('title', $post->meta_title ?? $post->title . ' — AdivoQ Blog')
@section('meta_description', $post->meta_description ?? $post->excerpt)

@section('content')
<article class="py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Back Link --}}
        <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-surface-400 hover:text-white text-sm mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Blog
        </a>

        {{-- Header --}}
        <header class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-brand-600/20 text-brand-400">
                    {{ ucfirst(str_replace('-', ' ', $post->category)) }}
                </span>
                <span class="text-surface-500 text-sm">{{ $post->published_at?->format('F d, Y') }}</span>
                <span class="text-surface-600">•</span>
                <span class="text-surface-500 text-sm">{{ number_format($post->views_count) }} views</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-bold text-white leading-tight">{{ $post->title }}</h1>

            @if($post->excerpt)
            <p class="mt-4 text-lg text-surface-400">{{ $post->excerpt }}</p>
            @endif
        </header>

        {{-- Cover Image --}}
        @if($post->cover_image)
        <div class="mb-10 rounded-xl overflow-hidden">
            <img src="{{ asset('uploads/blog/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full">
        </div>
        @endif

        {{-- Content --}}
        <div class="prose prose-invert prose-lg max-w-none">
            <style>
                .prose { color: #cbd5e1; }
                .prose h2 { color: #f8fafc; font-size: 1.5rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; }
                .prose h3 { color: #f1f5f9; font-size: 1.25rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; }
                .prose p { margin-bottom: 1.25rem; line-height: 1.75; }
                .prose a { color: #a78bfa; text-decoration: underline; }
                .prose a:hover { color: #8b5cf6; }
                .prose ul, .prose ol { margin-bottom: 1.25rem; padding-left: 1.5rem; }
                .prose li { margin-bottom: 0.5rem; }
                .prose strong { color: #f8fafc; }
                .prose blockquote { border-left: 4px solid #8b5cf6; padding-left: 1rem; font-style: italic; color: #94a3b8; }
                .prose code { background: #1e293b; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; }
                .prose pre { background: #1e293b; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; }
            </style>
            {!! $post->body !!}
        </div>

        {{-- Share --}}
        <div class="mt-12 pt-8 border-t border-surface-700">
            <p class="text-surface-400 text-sm mb-4">Share this article</p>
            <div class="flex items-center gap-3">
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 bg-surface-800 border border-surface-700 rounded-lg flex items-center justify-center text-surface-400 hover:text-white hover:border-surface-600 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($post->title) }}" target="_blank" class="w-10 h-10 bg-surface-800 border border-surface-700 rounded-lg flex items-center justify-center text-surface-400 hover:text-white hover:border-surface-600 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
                <button onclick="navigator.clipboard.writeText('{{ url()->current() }}'); alert('Link copied!');" class="w-10 h-10 bg-surface-800 border border-surface-700 rounded-lg flex items-center justify-center text-surface-400 hover:text-white hover:border-surface-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
        </div>
    </div>
</article>
@endsection