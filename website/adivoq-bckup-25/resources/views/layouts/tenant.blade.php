<!-- resources/views/layouts/tenant.blade.php -->
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — AdivoQ</title>

    @include('partials.fonts')
    @include('partials.tailwind-cdn')
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    @stack('styles')
</head>

<body class="bg-surface-900 text-surface-100 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64 bg-surface-900 border-r border-surface-800">
                <div class="flex items-center gap-2 h-16 px-6 border-b border-surface-800">
                    <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-indigo-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">A</span>
                    </div>
                    <span class="text-lg font-bold text-white">Adivo<span class="text-brand-400">Q</span></span>
                </div>
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    @include('partials.tenant-sidebar-links')
                </nav>
                @include('partials.sidebar-user')
            </div>
        </aside>

        {{-- Mobile Sidebar --}}
        <div x-show="sidebarOpen" x-cloak class="lg:hidden fixed inset-0 z-40">
            <div @click="sidebarOpen = false" class="absolute inset-0 bg-black/50"></div>
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-surface-900 border-r border-surface-800 overflow-y-auto"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full">
                <div class="flex items-center justify-between h-16 px-6 border-b border-surface-800">
                    <span class="text-lg font-bold text-white">Adivo<span class="text-brand-400">Q</span></span>
                    <button @click="sidebarOpen = false" class="text-surface-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <nav class="py-4 px-3 space-y-1">
                    @include('partials.tenant-sidebar-links')
                </nav>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top Header --}}
            <header class="h-16 bg-surface-900 border-b border-surface-800 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-surface-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-surface-100">@yield('page_title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-4">
                    <button class="relative text-surface-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </button>
                    <a href="{{ url('/dashboard/invoices/create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Invoice
                    </a>
                </div>
            </header>

            {{-- Page --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @include('partials.flash-messages')
                @yield('content')
            </main>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('chart-js')
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>