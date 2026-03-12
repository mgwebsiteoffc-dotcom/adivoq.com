@extends('errors.layout')

@section('title', '500 Server Error')
@section('panel_icon', 'fas fa-server')
@section('panel_title', 'Unexpected server error')
@section('panel_copy', 'The request reached the server, but the response could not be completed. Refresh or try again shortly.')

@section('content')
    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-600">Error 500</p>
    <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">Something broke on our side.</h1>
    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
        This is a server-side issue. The page is intentionally hidden behind a clean error screen so raw exception output is not exposed on live production.
    </p>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('home') }}" class="rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
            Back to Home
        </a>
        <a href="{{ url()->previous() }}" class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Try Previous Page
        </a>
    </div>
@endsection

@section('illustration')
    <div class="space-y-5">
        <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 p-4">
            <div class="mb-3 flex items-center gap-2 text-rose-200">
                <i class="fas fa-triangle-exclamation"></i>
                <span class="text-sm font-semibold">System response interrupted</span>
            </div>
            <div class="space-y-2">
                <div class="h-2 rounded-full bg-white/10">
                    <div class="h-2 w-4/5 rounded-full bg-rose-300/70"></div>
                </div>
                <div class="h-2 rounded-full bg-white/10">
                    <div class="h-2 w-3/5 rounded-full bg-amber-300/60"></div>
                </div>
                <div class="h-2 rounded-full bg-white/10">
                    <div class="h-2 w-2/5 rounded-full bg-indigo-300/60"></div>
                </div>
            </div>
        </div>

        <div class="flex h-40 items-center justify-center rounded-3xl bg-white/5 text-6xl text-rose-200">
            <i class="fas fa-server float-slow"></i>
        </div>
    </div>
@endsection
