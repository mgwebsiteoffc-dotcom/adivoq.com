@extends('layouts.public')
@section('title', 'Blog - InvoiceHero')
@section('meta_description', 'Tips, guides, and insights on invoicing, taxes, and growing your creator business in India.')
@push('schema')
    <script type="application/ld+json">
        {!! json_encode(\App\Support\PublicSeo::collectionPageSchema(
            'AdivoQ Blog',
            'Tips, guides, and insights on invoicing, taxes, and growing your creator business in India.',
            route('blog.index')
        ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode(\App\Support\PublicSeo::breadcrumbSchema([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Blog', 'url' => route('blog.index')],
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block px-4 py-1.5 bg-brand-50 text-brand-700 text-sm font-semibold rounded-full border border-brand-200 mb-4">BLOG</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">Creator Finance <span class="gradient-text">Insights</span></h1>
            <p class="mt-4 text-lg text-gray-600">Everything you need to know about invoicing, taxes, and managing your creator finances.</p>
        </div>

        {{-- Search & Filter --}}
        <div class="flex flex-col sm:flex-row items-center gap-4 mb-10 max-w-2xl mx-auto">
            <form method="GET" action="{{ route('blog.index') }}" class="flex-1 w-full">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..."
                           class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white">
                </div>
            </form>
        </div>

        {{-- Categories --}}
        @if($categories->count())
        <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
            <a href="{{ route('blog.index') }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition
                      {{ !request('category') ? 'gradient-bg text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                All
            </a>
            @foreach($categories as $category)
                <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition
                          {{ request('category') === $category->slug ? 'gradient-bg text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $category->name }} ({{ $category->posts_count }})
                </a>
            @endforeach
        </div>
        @endif

        {{-- Posts Grid --}}
        @if($posts->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($posts as $post)
                    <article class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        {{-- Cover Image --}}
                        <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-video bg-gray-100 overflow-hidden">
                            @if($post->cover_image)
                                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full gradient-bg opacity-20 flex items-center justify-center">
                                    <i class="fas fa-pen-nib text-brand-300 text-3xl"></i>
                                </div>
                            @endif
                        </a>

                        <div class="p-6">
                            @if($post->category)
                                <span class="inline-block px-3 py-1 bg-brand-50 text-brand-600 text-xs font-semibold rounded-full mb-3">
                                    {{ $post->category->name }}
                                </span>
                            @endif

                            <a href="{{ route('blog.show', $post->slug) }}" class="block">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-brand-600 transition line-clamp-2">{{ $post->title }}</h3>
                            </a>

                            @if($post->excerpt)
                                <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $post->excerpt }}</p>
                            @endif

                            <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-50">
                                <div class="flex items-center text-xs text-gray-500">
                                    <div class="w-6 h-6 bg-brand-100 rounded-full flex items-center justify-center mr-2">
                                        <span class="text-brand-600 font-bold text-xs">{{ substr($post->author->name ?? 'A', 0, 1) }}</span>
                                    </div>
                                    {{ $post->author->name ?? 'Admin' }}
                                </div>
                                <span class="text-xs text-gray-400">{{ $post->published_at?->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">{{ $posts->appends(request()->query())->links() }}</div>
        @else
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-pen-nib text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No posts found</h3>
                <p class="text-gray-600 text-sm">Check back soon for new articles!</p>
            </div>
        @endif
    </div>
</section>
@endsection
