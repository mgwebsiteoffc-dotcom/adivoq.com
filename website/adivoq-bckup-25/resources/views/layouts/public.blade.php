<!-- resources/views/layouts/public.blade.php -->
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'AdivoQ — Financial OS for Creators')</title>
    <meta name="description" content="@yield('meta_description', 'Track revenue, manage brand deals, automate payments, and handle taxes — all in one place.')">

    <meta property="og:title" content="@yield('title', 'AdivoQ')">
    <meta property="og:description" content="@yield('meta_description', 'Financial OS for Creators')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    @include('partials.fonts')
    @include('partials.tailwind-cdn')
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    @stack('styles')
</head>

<body class="bg-surface-900 text-surface-100 font-sans antialiased">

    {{-- Navbar --}}
    <nav x-data="{ mobileOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-surface-900/80 backdrop-blur-xl border-b border-surface-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-indigo-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">A</span>
                    </div>
                    <span class="text-xl font-bold text-white">Adivo<span class="text-brand-400">Q</span></span>
                </a>

                {{-- Desktop Links --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ url('/') }}" class="text-sm text-surface-400 hover:text-white transition-colors">Home</a>
                    <a href="{{ url('/blog') }}" class="text-sm text-surface-400 hover:text-white transition-colors">Blog</a>
                    <a href="{{ url('/tools/tax-calculator') }}" class="text-sm text-surface-400 hover:text-white transition-colors">Tools</a>
                    <a href="{{ url('/roadmap') }}" class="text-sm text-surface-400 hover:text-white transition-colors">Roadmap</a>
                    <a href="{{ url('/contact') }}" class="text-sm text-surface-400 hover:text-white transition-colors">Contact</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        @if(auth()->user()->is_system_admin)
                            <a href="{{ url('/admin/dashboard') }}" class="text-sm text-surface-400 hover:text-white">Admin Panel</a>
                        @else
                            <a href="{{ url('/dashboard') }}" class="text-sm text-surface-400 hover:text-white">Dashboard</a>
                        @endif
                    @else
                        <a href="{{ url('/login') }}" class="text-sm text-surface-400 hover:text-white">Log in</a>
                        <a href="{{ url('/register') }}" class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                            Get Started Free
                        </a>
                    @endauth
                </div>

                {{-- Mobile Toggle --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden text-surface-400 hover:text-white">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileOpen" x-cloak x-transition class="md:hidden py-4 border-t border-surface-800">
                <div class="flex flex-col gap-3">
                    <a href="{{ url('/') }}" class="text-sm text-surface-400 hover:text-white px-2 py-1.5">Home</a>
                    <a href="{{ url('/blog') }}" class="text-sm text-surface-400 hover:text-white px-2 py-1.5">Blog</a>
                    <a href="{{ url('/tools/tax-calculator') }}" class="text-sm text-surface-400 hover:text-white px-2 py-1.5">Tools</a>
                    <a href="{{ url('/roadmap') }}" class="text-sm text-surface-400 hover:text-white px-2 py-1.5">Roadmap</a>
                    <a href="{{ url('/contact') }}" class="text-sm text-surface-400 hover:text-white px-2 py-1.5">Contact</a>
                    <div class="border-t border-surface-800 pt-3 mt-1">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm text-brand-400 px-2 py-1.5">Dashboard</a>
                        @else
                            <a href="{{ url('/login') }}" class="text-sm text-surface-400 hover:text-white px-2 py-1.5 block">Log in</a>
                            <a href="{{ url('/register') }}" class="mt-2 block text-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg">Get Started Free</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Page Content --}}
    <main class="pt-16">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-surface-900 border-t border-surface-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-indigo-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">A</span>
                        </div>
                        <span class="text-lg font-bold text-white">Adivo<span class="text-brand-400">Q</span></span>
                    </div>
                    <p class="text-surface-400 text-sm">Financial OS for Creators. Track revenue, manage brand deals, and handle taxes in one place.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-surface-200 uppercase tracking-wider mb-4">Product</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/roadmap') }}" class="text-sm text-surface-400 hover:text-white">Roadmap</a></li>
                        <li><a href="{{ url('/templates') }}" class="text-sm text-surface-400 hover:text-white">Invoice Templates</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-surface-200 uppercase tracking-wider mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/blog') }}" class="text-sm text-surface-400 hover:text-white">Blog</a></li>
                        <li><a href="{{ url('/guides') }}" class="text-sm text-surface-400 hover:text-white">Creator Guides</a></li>
                        <li><a href="{{ url('/tools/tax-calculator') }}" class="text-sm text-surface-400 hover:text-white">Tax Calculator</a></li>
                        <li><a href="{{ url('/tools/invoice-generator') }}" class="text-sm text-surface-400 hover:text-white">Invoice Generator</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-surface-200 uppercase tracking-wider mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/contact') }}" class="text-sm text-surface-400 hover:text-white">Contact</a></li>
                        <li><a href="{{ url('/privacy-policy') }}" class="text-sm text-surface-400 hover:text-white">Privacy Policy</a></li>
                        <li><a href="{{ url('/terms-of-service') }}" class="text-sm text-surface-400 hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-surface-800 mt-8 pt-8 text-center">
                <p class="text-surface-500 text-sm">&copy; {{ date('Y') }} AdivoQ. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>