@extends('layouts.public')
@section('title', 'Create Account')

@section('content')
<section class="relative overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.16),transparent_24%),radial-gradient(circle_at_bottom_right,rgba(16,185,129,0.12),transparent_20%)]"></div>
    <div class="absolute inset-0 opacity-60" style="background-image:linear-gradient(rgba(99,102,241,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(99,102,241,0.05) 1px, transparent 1px); background-size:28px 28px;"></div>

    <div class="relative mx-auto flex min-h-[calc(100vh-10rem)] max-w-7xl items-center">
        <div class="grid w-full gap-8 lg:grid-cols-[1.05fr_0.95fr]">
            <section class="rounded-[2rem] border border-white/70 bg-white/85 p-8 shadow-2xl shadow-indigo-100/70 backdrop-blur md:p-12">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-indigo-700">
                    <i class="fas fa-bolt"></i>
                    InvoiceHero
                </div>

                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-indigo-600">Create Account</p>
                <h1 class="mt-2 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">Start your workspace.</h1>
                <p class="mt-2 max-w-2xl text-lg leading-8 text-slate-600">
                    Set up your account and start creating invoices in minutes. The form logic stays exactly the same. Only the experience is upgraded.
                </p>

                <div class="mt-4 rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                                   class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="business_name" class="mb-1 block text-sm font-medium text-gray-700">Business/Channel Name <span class="text-gray-400">(Optional)</span></label>
                            <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}"
                                   class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" id="password" name="password" required
                                   class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                            Create Account - It's Free
                        </button>

                        <p class="mt-4 text-center text-xs text-gray-500">
                            By signing up, you agree to our
                            <a href="{{ route('terms') }}" class="text-indigo-600">Terms of Service</a> and
                            <a href="{{ route('privacy') }}" class="text-indigo-600">Privacy Policy</a>.
                        </p>
                    </form>
                </div>

                <p class="mt-6 text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Sign in</a>
                </p>
            </section>

            <aside class="relative overflow-hidden rounded-[2rem] border border-slate-200/80 bg-slate-950 p-8 text-white shadow-2xl shadow-slate-300/40 md:p-10">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(99,102,241,0.35),_transparent_38%),radial-gradient(circle_at_bottom,_rgba(16,185,129,0.18),_transparent_28%)]"></div>
                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div>
                        <div class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-3xl bg-white/10 text-2xl text-indigo-200">
                            <i class="fas fa-user-plus"></i>
                        </div>

                        <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-6">
                            <div class="mb-4 flex items-center gap-3">
                                <div class="h-3 w-3 rounded-full bg-emerald-400"></div>
                                <div class="h-3 w-3 rounded-full bg-amber-400"></div>
                                <div class="h-3 w-3 rounded-full bg-rose-400"></div>
                            </div>

                            <div class="space-y-4">
                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">What you get</p>
                                    <div class="mt-4 space-y-3">
                                        <div class="flex items-start gap-3 rounded-xl bg-white/5 px-4 py-3">
                                            <i class="fas fa-file-invoice mt-1 text-indigo-200"></i>
                                            <div>
                                                <p class="text-sm font-semibold text-white">Professional invoicing</p>
                                                <p class="mt-1 text-xs text-slate-300">Create clean invoices for brands and clients.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3 rounded-xl bg-white/5 px-4 py-3">
                                            <i class="fas fa-wallet mt-1 text-emerald-200"></i>
                                            <div>
                                                <p class="text-sm font-semibold text-white">Payment tracking</p>
                                                <p class="mt-1 text-xs text-slate-300">Keep revenue, pending dues, and receipts organized.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3 rounded-xl bg-white/5 px-4 py-3">
                                            <i class="fas fa-comments mt-1 text-amber-200"></i>
                                            <div>
                                                <p class="text-sm font-semibold text-white">Reminder workflows</p>
                                                <p class="mt-1 text-xs text-slate-300">Follow up faster with built-in communication tools.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-emerald-500/10 p-4 text-sm text-slate-300">
                                    <p class="font-semibold text-white">Creator-first setup</p>
                                    <p class="mt-2 leading-6">You can begin with the same fields and workflow you already had, now presented in a stronger visual shell.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-sm text-slate-300">
                        <p class="font-semibold text-white">Ready in minutes</p>
                        <p class="mt-2 leading-6">No extra setup steps added. No logic changed. Just a cleaner public-facing experience.</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
