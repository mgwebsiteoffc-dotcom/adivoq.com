<!-- resources/views/layouts/auth.blade.php -->
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — AdivoQ</title>

    @include('partials.fonts')
    @include('partials.tailwind-cdn')
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>

<body class="bg-surface-900 text-surface-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2 mb-8">
            <div class="w-10 h-10 bg-gradient-to-br from-brand-500 to-indigo-500 rounded-xl flex items-center justify-center">
                <span class="text-white font-bold text-lg">A</span>
            </div>
            <span class="text-2xl font-bold text-white">Adivo<span class="text-brand-400">Q</span></span>
        </a>

        {{-- Card --}}
        <div class="w-full max-w-md bg-surface-800 border border-surface-700 rounded-2xl p-8">
            @include('partials.flash-messages')
            @yield('content')
        </div>

        <p class="mt-8 text-surface-500 text-sm">&copy; {{ date('Y') }} AdivoQ. All rights reserved.</p>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>