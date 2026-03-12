@extends('errors.layout')

@section('title', '404 Not Found')
@section('panel_icon', 'fas fa-map-signs')
@section('panel_title', 'Route not found')
@section('panel_copy', 'The page may have moved, the link may be outdated, or the URL may be typed incorrectly.')

@section('content')
    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-indigo-600">Error 404</p>
    <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">This page took a wrong turn.</h1>
    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
        The address you opened does not exist on this site anymore. You can head back home, browse the blog, or open the guides section.
    </p>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('home') }}" class="rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
            Go Home
        </a>
        <a href="{{ route('blog.index') }}" class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Open Blog
        </a>
        <a href="{{ route('guides.index') }}" class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Open Guides
        </a>
    </div>
@endsection

@section('illustration')
    <div class="space-y-5">
        <div class="flex items-center justify-between rounded-2xl bg-white/5 p-4">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Requested URL</p>
                <p class="mt-2 text-sm font-semibold text-white">{{ request()->path() }}</p>
            </div>
            <div class="text-4xl font-black text-indigo-200">404</div>
        </div>

        <div class="rounded-3xl bg-gradient-to-br from-indigo-500/20 to-cyan-400/10 p-6">
            <div class="flex items-center justify-center">
                <div class="relative h-40 w-40">
                    <div class="absolute inset-0 rounded-full border-2 border-dashed border-indigo-300/40"></div>
                    <div class="absolute inset-6 rounded-full border border-cyan-300/30"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-5xl text-indigo-200">
                        <i class="fas fa-compass"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
