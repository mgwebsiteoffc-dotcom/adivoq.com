<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin | InvoiceHero</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-900 text-gray-300 flex-shrink-0 overflow-y-auto">
            <div class="p-4 border-b border-gray-800">
                <h1 class="text-xl font-bold text-white"><i class="fas fa-shield-alt mr-2"></i>Admin Panel</h1>
            </div>
            <nav class="mt-4 px-2 space-y-1">
                @php
                    $adminNav = [
                        ['route' => 'admin.dashboard', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
                        ['route' => 'admin.tenants.index', 'icon' => 'fa-users', 'label' => 'Tenants'],
                        ['route' => 'admin.blog-posts.index', 'icon' => 'fa-blog', 'label' => 'Blog Posts'],
                        ['route' => 'admin.blog-categories.index', 'icon' => 'fa-tags', 'label' => 'Blog Categories'],
                        ['route' => 'admin.guides.index', 'icon' => 'fa-book', 'label' => 'Guides'],
                        ['route' => 'admin.roadmap.index', 'icon' => 'fa-road', 'label' => 'Roadmap'],
                        ['route' => 'admin.waitlist.index', 'icon' => 'fa-clipboard-list', 'label' => 'Waitlist'],
                        ['route' => 'admin.messages.index', 'icon' => 'fa-envelope', 'label' => 'Messages'],
                        ['route' => 'admin.admin-users.index', 'icon' => 'fa-user-shield', 'label' => 'Admin Users'],
                        ['route' => 'admin.activity-logs.index', 'icon' => 'fa-history', 'label' => 'Activity Logs'],
                        ['route' => 'admin.analytics.index', 'icon' => 'fa-chart-line', 'label' => 'Analytics'],
                        ['route' => 'admin.tracking-codes.index', 'icon' => 'fa-globe', 'label' => 'Tracking Codes'],
                        ['route' => 'admin.settings.index', 'icon' => 'fa-cog', 'label' => 'Settings'],
                    ];
                @endphp

                @foreach($adminNav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center px-3 py-2 rounded-lg text-sm transition
                              {{ request()->routeIs($item['route'] . '*') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas {{ $item['icon'] }} w-5 mr-3 text-center"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Top Bar --}}
            <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ auth()->guard('admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>