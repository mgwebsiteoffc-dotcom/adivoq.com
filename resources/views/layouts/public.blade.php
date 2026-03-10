<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InvoiceHero — Professional Invoicing for Content Creators')</title>
    <meta name="description" content="@yield('meta_description', 'Professional invoicing & payment management platform built exclusively for content creators. GST compliant, automated reminders, payment links & more.')">
    <meta name="keywords" content="invoice creator, creator invoice, youtube invoice, instagram invoice, GST invoice, content creator billing">

    {{-- OG Tags --}}
    <meta property="og:title" content="@yield('title', 'InvoiceHero — Invoicing for Creators')">
    <meta property="og:description" content="@yield('meta_description', 'Professional invoicing for content creators')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc',
                            400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca',
                            800: '#3730a3', 900: '#312e81',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out forwards',
                        'slide-up': 'slideUp 0.6s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(30px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } },
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .gradient-text { background: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .gradient-bg { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); }
        .glass { background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .hero-pattern { background-image: radial-gradient(circle at 1px 1px, rgba(99,102,241,0.08) 1px, transparent 0); background-size: 40px 40px; }
    </style>
    @stack('styles')
</head>
<body class="bg-white text-gray-900 font-sans antialiased" x-data="{ mobileMenu: false }" x-cloak>

    {{-- ========== NAVBAR ========== --}}
    <nav class="fixed top-0 w-full z-50 glass border-b border-gray-200/50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 lg:h-18">
                {{-- Logo --}}
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-9 h-9 gradient-bg rounded-xl flex items-center justify-center shadow-lg shadow-brand-500/25 group-hover:shadow-brand-500/40 transition-all">
                            <i class="fas fa-bolt text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-extrabold text-gray-900 tracking-tight">Invoice<span class="gradient-text">Hero</span></span>
                    </a>
                </div>

                {{-- Desktop Nav --}}
                <div class="hidden lg:flex items-center space-x-1">
                    @php
                        $navLinks = [
                            ['url' => route('tools.invoice-generator'), 'label' => 'Free Invoice', 'icon' => 'fa-file-invoice'],
                            ['url' => route('tools.tax-calculator'), 'label' => 'Tax Calculator', 'icon' => 'fa-calculator'],
                            ['url' => route('blog.index'), 'label' => 'Blog', 'icon' => 'fa-pen-nib'],
                            ['url' => route('guides.index'), 'label' => 'Guides', 'icon' => 'fa-book-open'],
                            ['url' => route('roadmap'), 'label' => 'Roadmap', 'icon' => 'fa-map'],
                            ['url' => route('contact'), 'label' => 'Contact', 'icon' => 'fa-envelope'],
                        ];
                    @endphp

                    @foreach($navLinks as $link)
                        <a href="{{ $link['url'] }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                  {{ request()->url() === $link['url'] ? 'text-brand-600 bg-brand-50' : 'text-gray-600 hover:text-brand-600 hover:bg-gray-50' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Desktop CTA --}}
                <div class="hidden lg:flex items-center space-x-3">
                    @auth
                        <a href="{{ route('dashboard.home') }}" class="px-5 py-2.5 gradient-bg text-white text-sm font-semibold rounded-xl hover:opacity-90 transition shadow-lg shadow-brand-500/25">
                            Dashboard <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:text-brand-600 transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 gradient-bg text-white text-sm font-semibold rounded-xl hover:opacity-90 transition shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40">
                            Start Free <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                        </a>
                    @endauth
                </div>

                {{-- Mobile Hamburger --}}
                <div class="flex items-center lg:hidden">
                    <button @click="mobileMenu = !mobileMenu" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <i class="fas" :class="mobileMenu ? 'fa-times' : 'fa-bars'" style="font-size: 20px;"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden bg-white border-t border-gray-100 shadow-xl">
            <div class="px-4 py-4 space-y-1">
                @foreach($navLinks as $link)
                    <a href="{{ $link['url'] }}" @click="mobileMenu = false"
                       class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition
                              {{ request()->url() === $link['url'] ? 'text-brand-600 bg-brand-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas {{ $link['icon'] }} w-5 text-center mr-3 text-gray-400"></i>
                        {{ $link['label'] }}
                    </a>
                @endforeach

                <hr class="my-3 border-gray-100">

                @auth
                    <a href="{{ route('dashboard.home') }}" class="block text-center px-4 py-3 gradient-bg text-white rounded-xl text-sm font-semibold shadow-lg">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block text-center px-4 py-3 border-2 border-gray-200 text-gray-700 rounded-xl text-sm font-semibold hover:border-brand-300 transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="block text-center px-4 py-3 gradient-bg text-white rounded-xl text-sm font-semibold shadow-lg mt-2">
                        Start Free — No Credit Card
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Spacer for fixed nav --}}
    <div class="h-16 lg:h-18"></div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="fixed top-20 right-4 z-50 max-w-sm animate-slide-up" id="flash-msg">
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-2xl p-4 shadow-xl flex items-start">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-green-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                <button onclick="this.closest('#flash-msg').remove()" class="ml-3 text-green-400 hover:text-green-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
        <script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 5000);</script>
    @endif

    @if(session('error'))
        <div class="fixed top-20 right-4 z-50 max-w-sm animate-slide-up" id="flash-err">
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 shadow-xl flex items-start">
                <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation text-red-600 text-sm"></i>
                </div>
                <div class="flex-1"><p class="text-sm font-medium">{{ session('error') }}</p></div>
                <button onclick="this.closest('#flash-err').remove()" class="ml-3 text-red-400 hover:text-red-600"><i class="fas fa-times text-xs"></i></button>
            </div>
        </div>
        <script>setTimeout(() => document.getElementById('flash-err')?.remove(), 5000);</script>
    @endif

    {{-- Page Content --}}
    @yield('content')

    {{-- ========== FOOTER ========== --}}
    <footer class="bg-gray-950 text-gray-400 relative overflow-hidden">
        {{-- Gradient Top --}}
        <div class="h-px bg-gradient-to-r from-transparent via-brand-500/50 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 lg:gap-12">
                {{-- Brand --}}
                <div class="col-span-2 md:col-span-4 lg:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5 mb-5">
                        <div class="w-9 h-9 gradient-bg rounded-xl flex items-center justify-center">
                            <i class="fas fa-bolt text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-extrabold text-white">Invoice<span class="text-brand-400">Hero</span></span>
                    </a>
                    <p class="text-sm leading-relaxed text-gray-500 max-w-xs">
                        The #1 invoicing platform built for content creators. Manage brands, automate payments, stay GST compliant.
                    </p>
                    <div class="flex items-center space-x-4 mt-6">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-brand-600 hover:text-white transition"><i class="fab fa-twitter text-sm"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-brand-600 hover:text-white transition"><i class="fab fa-instagram text-sm"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-brand-600 hover:text-white transition"><i class="fab fa-youtube text-sm"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-brand-600 hover:text-white transition"><i class="fab fa-linkedin-in text-sm"></i></a>
                    </div>
                </div>

                {{-- Product --}}
                <div>
                    <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-5">Product</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('tools.invoice-generator') }}" class="hover:text-white transition">Free Invoice Generator</a></li>
                        <li><a href="{{ route('tools.tax-calculator') }}" class="hover:text-white transition">Tax Calculator</a></li>
                        <li><a href="{{ route('tools.templates') }}" class="hover:text-white transition">Invoice Templates</a></li>
                        <li><a href="{{ route('roadmap') }}" class="hover:text-white transition">Product Roadmap</a></li>
                        <li><a href="{{ route('home') }}#pricing" class="hover:text-white transition">Pricing</a></li>
                    </ul>
                </div>

                {{-- Resources --}}
                <div>
                    <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-5">Resources</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a></li>
                        <li><a href="{{ route('guides.index') }}" class="hover:text-white transition">Creator Guides</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact Us</a></li>
                        <li><a href="{{ route('home') }}#faq" class="hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-5">Legal</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="{{ route('refund') }}" class="hover:text-white transition">Refund Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-14 pt-8 flex flex-col md:flex-row justify-between items-center text-sm">
                <p>&copy; {{ date('Y') }} InvoiceHero. All rights reserved.</p>
                <p class="mt-3 md:mt-0 flex items-center">
                    Made with <i class="fas fa-heart text-red-500 mx-1.5 text-xs"></i> for Creators in India
                </p>
            </div>
        </div>
    </footer>

    {{-- Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>

    {{-- Navbar scroll effect --}}
    <script>
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-sm');
                nav.style.background = 'rgba(255,255,255,0.95)';
            } else {
                nav.classList.remove('shadow-sm');
                nav.style.background = 'rgba(255,255,255,0.8)';
            }
        });
    </script>

    @stack('scripts')
</body>
</html>