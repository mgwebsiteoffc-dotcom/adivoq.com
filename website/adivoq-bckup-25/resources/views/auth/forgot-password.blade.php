<!-- resources/views/auth/forgot-password.blade.php -->
@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <h2 class="text-2xl font-bold text-center text-white mb-2">Forgot password?</h2>
    <p class="text-surface-400 text-center mb-8">Enter your email and we'll send you a reset link</p>

    @if (session('status'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-lg text-sm mb-6">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

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

        <button
            type="submit"
            class="w-full bg-brand-600 text-white font-medium py-2.5 px-4 rounded-lg hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-surface-800 transition-all text-sm"
        >
            Send reset link
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-surface-400">
        <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 font-medium">← Back to login</a>
    </p>
@endsection