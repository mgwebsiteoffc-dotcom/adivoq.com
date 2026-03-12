@extends('errors.layout')

@section('title', '503 Maintenance')
@section('panel_icon', 'fas fa-screwdriver-wrench')
@section('panel_title', 'Maintenance in progress')
@section('panel_copy', 'The platform is temporarily paused for updates. Admin routes can still remain accessible as configured.')

@section('content')
    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-amber-600">Error 503</p>
    <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">We are tuning things up.</h1>
    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
        The site is in maintenance mode right now. This page prevents users from seeing a blank or raw framework error while updates are being applied.
    </p>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('home') }}" class="rounded-2xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            Refresh Home Later
        </a>
        <a href="{{ route('contact') }}" class="rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
            Contact Support
        </a>
    </div>
@endsection

@section('illustration')
    <div class="space-y-5">
        <div class="rounded-3xl bg-gradient-to-br from-amber-400/20 to-indigo-400/15 p-6">
            <div class="flex items-center justify-center">
                <div class="relative h-40 w-40">
                    <div class="absolute inset-0 rounded-[2rem] border border-dashed border-amber-200/40 rotate-6"></div>
                    <div class="absolute inset-3 rounded-[2rem] border border-white/10 -rotate-6"></div>
                    <div class="absolute inset-0 flex items-center justify-center gap-4 text-5xl text-amber-200">
                        <i class="fas fa-gear fa-spin" style="animation-duration: 5s;"></i>
                        <i class="fas fa-wrench -rotate-12"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300">
            <p class="font-semibold text-white">Maintenance mode is active.</p>
            <p class="mt-2">Please retry in a few minutes.</p>
        </div>
    </div>
@endsection
