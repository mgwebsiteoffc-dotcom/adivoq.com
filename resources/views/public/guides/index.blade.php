@extends('layouts.public')
@section('title', 'Creator Guides — InvoiceHero')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block px-4 py-1.5 bg-purple-50 text-purple-700 text-sm font-semibold rounded-full border border-purple-200 mb-4">GUIDES</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">Step-by-step <span class="gradient-text">creator guides</span></h1>
            <p class="mt-4 text-lg text-gray-600">Learn everything about invoicing, GST, TDS, and managing your creator finances.</p>
        </div>

        @if($categories->count())
        <div class="flex flex-wrap justify-center gap-2 mb-10">
            <a href="{{ route('guides.index') }}" class="px-4 py-2 rounded-full text-sm font-medium transition {{ !request('category') ? 'gradient-bg text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('guides.index', ['category' => $cat]) }}" class="px-4 py-2 rounded-full text-sm font-medium transition {{ request('category') === $cat ? 'gradient-bg text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">{{ $cat }}</a>
            @endforeach
        </div>
        @endif

        @if($guides->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($guides as $guide)
                    <a href="{{ route('guides.show', $guide->slug) }}" class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="aspect-[16/9] bg-gradient-to-br from-brand-100 to-purple-100 flex items-center justify-center">
                            @if($guide->cover_image)
                                <img src="{{ asset('storage/' . $guide->cover_image) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-book-open text-brand-400 text-4xl"></i>
                            @endif
                        </div>
                        <div class="p-6">
                            @if($guide->category)
                                <span class="inline-block px-3 py-1 bg-purple-50 text-purple-600 text-xs font-semibold rounded-full mb-3">{{ $guide->category }}</span>
                            @endif
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-brand-600 transition line-clamp-2">{{ $guide->title }}</h3>
                            @if($guide->description)
                                <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $guide->description }}</p>
                            @endif
                            <div class="mt-4 flex items-center text-sm text-brand-600 font-semibold">
                                Read Guide <i class="fas fa-arrow-right ml-2 text-xs group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-12">{{ $guides->appends(request()->query())->links() }}</div>
        @else
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-book-open text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No guides yet</h3>
                <p class="text-gray-600 text-sm mt-2">We're writing them! Check back soon.</p>
            </div>
        @endif
    </div>
</section>
@endsection