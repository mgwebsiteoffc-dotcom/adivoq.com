<!-- resources/views/auth/register.blade.php -->
@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
    <h2 class="text-2xl font-bold text-center text-white mb-2">Create your account</h2>
    <p class="text-surface-400 text-center mb-8">Start managing your creator finances today</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-surface-300 mb-1.5">Full name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
                placeholder="Your name"
            >
            @error('name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-surface-300 mb-1.5">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
                placeholder="you@example.com"
            >
            @error('email')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Business Name --}}
        <div>
            <label for="business_name" class="block text-sm font-medium text-surface-300 mb-1.5">Business / Studio name</label>
            <input
                type="text"
                id="business_name"
                name="business_name"
                value="{{ old('business_name') }}"
                required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
                placeholder="e.g., Amit's Creator Studio"
            >
            @error('business_name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-surface-300 mb-1.5">Password</label>
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

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-surface-300 mb-1.5">Confirm password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
                placeholder="••••••••"
            >
        </div>

        {{-- Terms --}}
        <div class="flex items-start gap-2">
            <input
                type="checkbox"
                id="terms"
                name="terms"
                required
                class="w-4 h-4 mt-0.5 rounded border-surface-600 bg-surface-900 text-brand-600 focus:ring-brand-500"
            >
            <label for="terms" class="text-sm text-surface-400">
                I agree to the
                <a href="{{ url('/terms-of-service') }}" class="text-brand-400 hover:underline" target="_blank">Terms of Service</a>
                and
                <a href="{{ url('/privacy-policy') }}" class="text-brand-400 hover:underline" target="_blank">Privacy Policy</a>
            </label>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full bg-brand-600 text-white font-medium py-2.5 px-4 rounded-lg hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-surface-800 transition-all text-sm"
        >
            Create account
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-surface-400">
        Already have an account?
        <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 font-medium">Log in</a>
    </p>
@endsection