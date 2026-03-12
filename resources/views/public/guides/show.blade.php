@extends('layouts.public')
@section('title', $guide->title . ' - InvoiceHero Guides')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
            <i class="fas fa-chevron-right text-xs text-gray-300"></i>
            <a href="{{ route('guides.index') }}" class="hover:text-brand-600">Guides</a>
        </nav>

        <header class="mb-12">
            @if($guide->category)
                <span class="inline-block px-3 py-1.5 bg-purple-50 text-purple-600 text-sm font-semibold rounded-full mb-4">{{ $guide->category }}</span>
            @endif
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $guide->title }}</h1>
            @if($guide->description)
                <p class="mt-4 text-lg text-gray-600">{{ $guide->description }}</p>
            @endif
        </header>

        {{-- Steps --}}
        @if($guide->steps->count())
            <div class="space-y-8">
                @foreach($guide->steps as $index => $step)
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 hover:shadow-lg transition-shadow duration-300" id="step-{{ $index + 1 }}">
                        <div class="flex items-start">
                            <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0 mr-5 mt-1">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $step->title }}</h2>
                                <div class="prose prose-sm max-w-none prose-p:text-gray-700 prose-headings:text-gray-900 prose-a:text-brand-600">
                                    {!! $step->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- CTA --}}
        <div class="mt-16 gradient-bg rounded-2xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold">Found this guide helpful?</h3>
            <p class="mt-2 text-white/80">Start using InvoiceHero to apply what you've learned.</p>
            <a href="{{ route('register') }}" class="inline-block mt-5 px-8 py-3 bg-white text-brand-600 font-bold rounded-xl hover:shadow-xl transition">
                Get Started Free <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endsection
