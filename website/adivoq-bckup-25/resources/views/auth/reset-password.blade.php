<!-- resources/views/auth/reset-password.blade.php -->
@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <h2 class="text-2xl font-bold text-center text-white mb-2">Reset password</h2>
    <p class="text-surface-400 text-center mb-8">Enter your new password below</p>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="block text-sm font-medium text-surface-300 mb-1.5">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
            >
            @error('email')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-surface-300 mb-1.5">New password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
            >
            @error('password')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-surface-300 mb-1.5">Confirm new password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
            >
        </div>

        <button
            type="submit"
            class="w-full bg-brand-600 text-white font-medium py-2.5 px-4 rounded-lg hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-surface-800 transition-all text-sm"
        >
            Reset password
        </button>
    </form>
@endsection