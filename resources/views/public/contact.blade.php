@extends('layouts.public')
@section('title', 'Contact Us — InvoiceHero')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block px-4 py-1.5 bg-brand-50 text-brand-700 text-sm font-semibold rounded-full border border-brand-200 mb-4">CONTACT</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">Get in <span class="gradient-text">touch</span></h1>
            <p class="mt-4 text-lg text-gray-600">Have a question, feedback, or need help? We'd love to hear from you.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12">
            {{-- Contact Info --}}
            <div class="lg:col-span-2 space-y-6">
                @foreach([
                    ['icon' => 'fa-envelope', 'color' => 'brand', 'title' => 'Email Us', 'text' => 'connect@adivoq.com', 'sub' => 'We reply within 24 hours'],
                    ['icon' => 'fa-brands fa-whatsapp', 'color' => 'green', 'title' => 'WhatsApp', 'text' => '+91 89537 49734', 'sub' => 'Quick support on WhatsApp'],
                    ['icon' => 'fa-clock', 'color' => 'orange', 'title' => 'Support Hours', 'text' => 'Mon - Fri, 10AM - 6PM IST', 'sub' => 'Except public holidays'],
                ] as $info)
                    @php
                        $iconColors = ['brand' => 'bg-brand-100 text-brand-600', 'green' => 'bg-green-100 text-green-600', 'orange' => 'bg-orange-100 text-orange-600'];
                    @endphp
                    <div class="flex items-start">
                        <div class="w-12 h-12 {{ $iconColors[$info['color']] }} rounded-xl flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas {{ $info['icon'] }}"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">{{ $info['title'] }}</h3>
                            <p class="text-sm text-gray-700 font-medium mt-0.5">{{ $info['text'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $info['sub'] }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Follow Us</h3>
                    <div class="flex space-x-3">
                        @foreach([['icon' => 'fa-whatsapp', 'url' => 'https://wa.me/918953749734?text=Hello%20I%20want%20more%20details%20about%AdivoQ'], ['icon' => 'fa-instagram', 'url' => 'https://www.instagram.com/adivoq/'], ['icon' => 'fa-facebook', 'url' => 'https://www.facebook.com/profile.php?id=61588558224081'], ['icon' => 'fa-linkedin-in', 'url' => '#']] as $social)
                            <a href="{{ $social['url'] }}" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:gradient-bg hover:text-white transition">
                                <i class="fab {{ $social['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8 shadow-sm">
                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 @error('name') border-red-500 @enderror">
                                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 @error('email') border-red-500 @enderror">
                                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subject</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" placeholder="How can we help?"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message *</label>
                            <textarea name="message" rows="5" required placeholder="Tell us what's on your mind..."
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                            @error('message') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 gradient-bg text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition">
                            <i class="fas fa-paper-plane mr-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection