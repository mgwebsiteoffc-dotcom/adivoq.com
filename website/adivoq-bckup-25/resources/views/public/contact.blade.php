<!-- resources/views/public/contact.blade.php -->
@extends('layouts.public')

@section('title', 'Contact Us — AdivoQ')
@section('meta_description', 'Get in touch with the AdivoQ team. We\'re here to help you manage your creator finances.')

@section('content')
<div class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            {{-- Header --}}
            <div class="text-center mb-12">
                <h1 class="text-3xl sm:text-4xl font-bold text-white">Get in Touch</h1>
                <p class="mt-4 text-surface-400">Have a question or feedback? We'd love to hear from you.</p>
            </div>

            {{-- Contact Form --}}
            <div class="bg-surface-800 border border-surface-700 rounded-2xl p-8">
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-surface-300 mb-1.5">Your Name</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
                                placeholder="John Doe"
                            >
                            @error('name')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-surface-300 mb-1.5">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
                                placeholder="john@example.com"
                            >
                            @error('email')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-surface-300 mb-1.5">Subject</label>
                        <select
                            id="subject"
                            name="subject"
                            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm"
                        >
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Technical Support">Technical Support</option>
                            <option value="Billing Question">Billing Question</option>
                            <option value="Feature Request">Feature Request</option>
                            <option value="Partnership">Partnership</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-surface-300 mb-1.5">Message</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition-all text-sm resize-none"
                            placeholder="How can we help you?"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-brand-600 text-white font-semibold py-3 px-4 rounded-xl hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-surface-800 transition-all text-sm"
                    >
                        Send Message
                    </button>
                </form>
            </div>

            {{-- Contact Info --}}
            <div class="mt-12 grid sm:grid-cols-3 gap-6 text-center">
                <div>
                    <div class="w-12 h-12 bg-brand-600/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-surface-400 text-sm">Email</p>
                    <a href="mailto:hello@adivoq.com" class="text-white font-medium hover:text-brand-400">hello@adivoq.com</a>
                </div>

                <div>
                    <div class="w-12 h-12 bg-brand-600/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h8z"/>
                        </svg>
                    </div>
                    <p class="text-surface-400 text-sm">Twitter/X</p>
                    <a href="https://twitter.com/adivoq" target="_blank" class="text-white font-medium hover:text-brand-400">@adivoq</a>
                </div>

                <div>
                    <div class="w-12 h-12 bg-brand-600/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-surface-400 text-sm">Response Time</p>
                    <p class="text-white font-medium">Within 24 hours</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection