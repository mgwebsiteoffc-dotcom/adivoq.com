@extends('layouts.public')
@section('title', 'Invoice Templates — InvoiceHero')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block px-4 py-1.5 bg-brand-50 text-brand-700 text-sm font-semibold rounded-full border border-brand-200 mb-4">TEMPLATES</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">Beautiful <span class="gradient-text">Invoice Templates</span></h1>
            <p class="mt-4 text-lg text-gray-600">Professional invoice designs that make your brand look polished. All customizable.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @foreach($templates as $template)
                <div class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    {{-- Preview --}}
                    <div class="aspect-[3/4] bg-gray-50 p-6 relative overflow-hidden">
                        {{-- Simulated Invoice Preview --}}
                        <div class="bg-white rounded-lg shadow-sm border p-4 h-full flex flex-col" style="font-size: 8px;">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="w-8 h-8 rounded-lg mb-2" style="background: {{ $template['color'] }}"></div>
                                    <div class="w-20 h-2 bg-gray-200 rounded mb-1"></div>
                                    <div class="w-16 h-1.5 bg-gray-100 rounded"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-black" style="color: {{ $template['color'] }}">INVOICE</div>
                                    <div class="w-14 h-1.5 bg-gray-200 rounded ml-auto mt-1"></div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="w-full h-1.5 rounded mb-2" style="background: {{ $template['color'] }}20"></div>
                                <div class="space-y-2 mt-3">
                                    @for($i = 0; $i < 3; $i++)
                                        <div class="flex justify-between">
                                            <div class="w-24 h-1.5 bg-gray-100 rounded"></div>
                                            <div class="w-10 h-1.5 bg-gray-200 rounded"></div>
                                        </div>
                                    @endfor
                                </div>
                                <div class="mt-4 text-right">
                                    <div class="w-16 h-2 rounded ml-auto" style="background: {{ $template['color'] }}"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Hover Overlay --}}
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-gray-900 font-bold rounded-xl shadow-xl hover:scale-105 transition-transform text-sm">
                                Use This Template <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>

                    <div class="p-5">
                        <h3 class="text-base font-bold text-gray-900">{{ $template['name'] }}</h3>
                        <p class="text-xs text-gray-600 mt-1">{{ $template['description'] }}</p>
                        <a href="{{ route('register') }}" class="inline-flex items-center text-sm font-semibold text-brand-600 mt-3 hover:text-brand-700 transition">
                            Use Template <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16 text-center gradient-bg rounded-2xl p-10 text-white">
            <h3 class="text-2xl font-bold">All templates included in every plan</h3>
            <p class="mt-2 text-white/80">Customize colors, add your logo, and make it yours.</p>
            <a href="{{ route('register') }}" class="inline-block mt-5 px-8 py-3 bg-white text-brand-600 font-bold rounded-xl hover:shadow-xl transition">
                Start Free <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endsection