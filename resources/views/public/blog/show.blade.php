@extends('layouts.public')
@section('title', ($post->meta_title ?: $post->title) . ' - InvoiceHero Blog')
@section('meta_description', $post->meta_description ?: $post->excerpt)
@section('meta_image', $post->cover_image ? asset('storage/' . $post->cover_image) : asset('favicon.ico'))
@push('schema')
    <script type="application/ld+json">
        {!! json_encode(\App\Support\PublicSeo::blogPostingSchema($post), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode(\App\Support\PublicSeo::breadcrumbSchema(array_filter([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Blog', 'url' => route('blog.index')],
            $post->category ? ['name' => $post->category->name, 'url' => route('blog.index', ['category' => $post->category->slug])] : null,
            ['name' => $post->title, 'url' => route('blog.show', $post->slug)],
        ])), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
<article class="py-12 lg:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
            <i class="fas fa-chevron-right text-xs text-gray-300"></i>
            <a href="{{ route('blog.index') }}" class="hover:text-brand-600">Blog</a>
            @if($post->category)
                <i class="fas fa-chevron-right text-xs text-gray-300"></i>
                <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-brand-600">{{ $post->category->name }}</a>
            @endif
        </nav>

        {{-- Header --}}
        <header class="mb-10">
            @if($post->category)
                <span class="inline-block px-3 py-1.5 bg-brand-50 text-brand-600 text-sm font-semibold rounded-full mb-4">{{ $post->category->name }}</span>
            @endif
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">{{ $post->title }}</h1>

            <div class="flex flex-wrap items-center gap-4 mt-6 text-sm text-gray-500">
                <div class="flex items-center">
                    <div class="w-8 h-8 gradient-bg rounded-full flex items-center justify-center mr-2">
                        <span class="text-white font-bold text-xs">{{ substr($post->author->name ?? 'A', 0, 1) }}</span>
                    </div>
                    <span class="font-medium text-gray-700">{{ $post->author->name ?? 'Admin' }}</span>
                </div>
                <span><i class="far fa-calendar mr-1"></i>{{ $post->published_at?->format('M d, Y') }}</span>
                <span><i class="far fa-clock mr-1"></i>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read</span>
            </div>
        </header>

        {{-- Cover Image --}}
        @if($post->cover_image)
            <div class="aspect-video rounded-2xl overflow-hidden mb-10 shadow-lg">
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        {{-- Content --}}
        <div class="prose prose-lg max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-p:text-gray-700 prose-a:text-brand-600 prose-a:no-underline hover:prose-a:underline prose-img:rounded-xl">
            {!! $post->content !!}
        </div>

        {{-- Share --}}
        <div class="flex items-center justify-between mt-12 pt-8 border-t border-gray-200">
            <span class="text-sm font-semibold text-gray-700">Share this article:</span>
            <div class="flex items-center space-x-3">
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank"
                   class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-blue-50 hover:text-blue-500 transition">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank"
                   class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-blue-50 hover:text-blue-700 transition">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank"
                   class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-green-50 hover:text-green-600 transition">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <button onclick="navigator.clipboard.writeText('{{ url()->current() }}'); this.innerHTML='<i class=\'fas fa-check\'></i>'; setTimeout(() => this.innerHTML='<i class=\'fas fa-link\'></i>', 2000)"
                        class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition">
                    <i class="fas fa-link"></i>
                </button>
            </div>
        </div>

        {{-- Related Posts --}}
        @if($relatedPosts->count())
            <div class="mt-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-8">Related Articles</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $related)
                        <a href="{{ route('blog.show', $related->slug) }}" class="group bg-gray-50 rounded-2xl p-5 hover:shadow-lg transition-all duration-300">
                            <h4 class="text-sm font-bold text-gray-900 group-hover:text-brand-600 transition line-clamp-2">{{ $related->title }}</h4>
                            <p class="mt-2 text-xs text-gray-500">{{ $related->published_at?->format('M d, Y') }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- CTA --}}
        <div class="mt-16 gradient-bg rounded-2xl p-8 lg:p-10 text-center text-white">
            <h3 class="text-2xl font-bold">Ready to streamline your invoicing?</h3>
            <p class="mt-2 text-white/80 text-sm">Join 2,000+ creators already using InvoiceHero.</p>
            <a href="{{ route('register') }}" class="inline-block mt-5 px-8 py-3 bg-white text-brand-600 font-bold rounded-xl hover:shadow-xl transition">
                Start Free Now <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</article>
@endsection
