<!-- resources/views/auth/login.blade.php -->
@extends('layouts.auth')

@section('title', 'Log In')

@section('content')
    <h2 class="text-2xl font-bold text-center text-white mb-2">Welcome back</h2>
    <p class="text-surface-400 text-center mb-8">Log in to your AdivoQ account</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-surface-300 mb-1.5">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
                placeholder="you@example.com"
            >
            @error('email')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-surface-300">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs text-brand-400 hover:text-brand-300">Forgot password?</a>
            </div>
            <input
                type="password"
                id="password"
                name="password"
                required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
                placeholder="••••••••"
            >
            @error('password')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center gap-2">
            <input
                type="checkbox"
                id="remember"
                name="remember"
                class="w-4 h-4 rounded border-surface-600 bg-surface-900 text-brand-600 focus:ring-brand-500"
            >
            <label for="remember" class="text-sm text-surface-400">Remember me</label>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full bg-brand-600 text-white font-medium py-2.5 px-4 rounded-lg hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-surface-800 transition-all text-sm"
        >
            Log in
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-surface-400">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-brand-400 hover:text-brand-300 font-medium">Sign up free</a>
    </p>
@endsection