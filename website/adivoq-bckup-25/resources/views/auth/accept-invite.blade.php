@extends('layouts.auth')

@section('title', 'Accept Invitation')

@section('content')
    <h2 class="text-2xl font-bold text-center text-white mb-2">Accept Invitation</h2>
    <p class="text-surface-400 text-center mb-8">Set your name and password to activate your account.</p>

    <form method="POST" action="{{ route('accept-invite', $token) }}" class="space-y-5">
        @csrf

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-surface-300 mb-1.5">Your Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm">
        </div>

        {{-- Email (readonly) --}}
        <div>
            <label class="block text-sm font-medium text-surface-300 mb-1.5">Email</label>
            <input type="email" value="{{ $user->email }}" readonly
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-400 text-sm cursor-not-allowed">
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-surface-300 mb-1.5">Set Password</label>
            <input type="password" name="password" required
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm">
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-sm font-medium text-surface-300 mb-1.5">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm">
        </div>

        <button type="submit"
                class="w-full bg-brand-600 text-white font-medium py-2.5 px-4 rounded-lg hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-surface-800 transition-all text-sm">
            Activate Account
        </button>
    </form>
@endsection