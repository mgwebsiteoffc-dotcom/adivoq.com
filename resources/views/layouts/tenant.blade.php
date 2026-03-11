<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - InvoiceHero</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')

    <!-- Third-Party Tracking Codes -->
    {!! \App\Services\EventTrackingService::renderAllEnabled() !!}
</head>

<body class="bg-gray-50" x-data="{ sidebarOpen: true }" x-cloak>
<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
           class="bg-white border-r border-gray-200 flex-shrink-0 overflow-y-auto transition-all duration-300 relative">

        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <a href="{{ route('dashboard.home') }}" class="flex items-center">
                <i class="fas fa-file-invoice-dollar text-2xl text-indigo-600"></i>
                <span x-show="sidebarOpen" class="ml-2 text-lg font-black text-gray-900">InvoiceHero</span>
            </a>

            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-gray-700">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        @php
            $u = auth()->user();
            $tenant = $currentTenant ?? $u->tenant;

            $nav = [];

            $nav[] = ['route' => 'dashboard.home', 'icon' => 'fa-home', 'label' => 'Dashboard', 'match' => 'dashboard.home'];

            // Campaign side (owner/manager/editor)
            if($u->canManageCampaigns()){
                $nav[] = ['route' => 'dashboard.brands.index', 'icon' => 'fa-building', 'label' => 'Brands', 'match' => 'dashboard.brands.*'];
                $nav[] = ['route' => 'dashboard.campaigns.index', 'icon' => 'fa-bullhorn', 'label' => 'Campaigns', 'match' => 'dashboard.campaigns.*'];
            }

            // Finance side (owner/manager/accountant)
            if($u->canManageFinances()){
                $nav[] = ['route' => 'dashboard.invoices.index', 'icon' => 'fa-file-invoice', 'label' => 'Invoices', 'match' => 'dashboard.invoices.*'];
                $nav[] = ['route' => 'dashboard.payments.index', 'icon' => 'fa-credit-card', 'label' => 'Payments', 'match' => 'dashboard.payments.*'];
                $nav[] = ['route' => 'dashboard.expenses.index', 'icon' => 'fa-receipt', 'label' => 'Expenses', 'match' => 'dashboard.expenses.*'];
                $nav[] = ['route' => 'dashboard.reports.index', 'icon' => 'fa-chart-bar', 'label' => 'Reports', 'match' => 'dashboard.reports.*'];
                $nav[] = ['route' => 'dashboard.billing.index', 'icon' => 'fa-crown', 'label' => 'Billing', 'match' => 'dashboard.billing.*'];
            }

            // Team + settings (owner/manager)
            if($u->canManageTeam()){
                $nav[] = ['route' => 'dashboard.team.index', 'icon' => 'fa-users', 'label' => 'Team', 'match' => 'dashboard.team.*'];
                $nav[] = ['route' => 'dashboard.settings.index', 'icon' => 'fa-cog', 'label' => 'Settings', 'match' => 'dashboard.settings.*'];
            }

            $taxOpenDefault = request()->routeIs('dashboard.tax.*') || request()->routeIs('dashboard.tds-certificates.*');
        @endphp

        <nav class="mt-4 px-2 space-y-1">
            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm transition
                          {{ request()->routeIs($item['match']) ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                   title="{{ $item['label'] }}">
                    <i class="fas {{ $item['icon'] }} w-5 text-center {{ request()->routeIs($item['match']) ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                    <span x-show="sidebarOpen" class="ml-3">{{ $item['label'] }}</span>
                </a>
            @endforeach

            

            {{-- Tax submenu (finance roles only) --}}
            @if($u->canManageFinances())
                <div class="mt-2" x-data="{ open: {{ $taxOpenDefault ? 'true' : 'false' }} }">
                    <button type="button"
                            @click="open = !open"
                            class="w-full flex items-center px-3 py-2.5 rounded-lg text-sm transition
                                {{ $taxOpenDefault ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-percentage w-5 text-center {{ $taxOpenDefault ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                        <span x-show="sidebarOpen" class="ml-3 flex-1 text-left">Tax</span>
                        <i x-show="sidebarOpen" class="fas fa-chevron-down text-xs text-gray-400 transition-transform"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-collapse x-cloak class="mt-1 space-y-1 pl-2">
                        <a href="{{ route('dashboard.tax.index') }}"
                           class="flex items-center px-3 py-2 rounded-lg text-sm transition
                                  {{ request()->routeIs('dashboard.tax.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-chart-pie w-5 text-center text-gray-400"></i>
                            <span x-show="sidebarOpen" class="ml-3">Tax Dashboard</span>
                        </a>

                        <a href="{{ route('dashboard.tax.returns') }}"
                           class="flex items-center px-3 py-2 rounded-lg text-sm transition
                                  {{ request()->routeIs('dashboard.tax.returns') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-file-export w-5 text-center text-gray-400"></i>
                            <span x-show="sidebarOpen" class="ml-3">GST Returns (GSTR)</span>
                        </a>

                        <a href="{{ route('dashboard.tds-certificates.index') }}"
                           class="flex items-center px-3 py-2 rounded-lg text-sm transition
                                  {{ request()->routeIs('dashboard.tds-certificates.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-certificate w-5 text-center text-gray-400"></i>
                            <span x-show="sidebarOpen" class="ml-3">TDS Certificates</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>

        {{-- Plan Info --}}
        <div x-show="sidebarOpen" class="absolute bottom-0 w-full p-4 border-t border-gray-100 bg-gray-50">
            <div class="text-xs text-gray-500">
                <span class="inline-block px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">
                    {{ ucfirst($tenant->plan ?? 'free') }} Plan
                </span>
                @if($tenant->isOnTrial())
                    <span class="block mt-1">Trial ends {{ $tenant->trial_ends_at->diffForHumans() }}</span>
                @endif
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black text-gray-900">@yield('page_title', 'Dashboard')</h2>
                @hasSection('page_subtitle')
                    <p class="text-sm text-gray-500">@yield('page_subtitle')</p>
                @endif
            </div>

            <div class="flex items-center gap-3">
                {{-- Role badge --}}
                <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-black
                    {{ $u->role === 'owner' ? 'bg-indigo-100 text-indigo-700' : '' }}
                    {{ $u->role === 'manager' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $u->role === 'accountant' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $u->role === 'editor' ? 'bg-purple-100 text-purple-700' : '' }}
                    {{ $u->role === 'viewer' ? 'bg-gray-100 text-gray-700' : '' }}">
                    <i class="fas fa-user-tag mr-2"></i>{{ ucfirst($u->role) }}
                </span>

                {{-- Back to Admin (impersonation) --}}
                @if(session('admin_impersonating'))
                    <form method="POST" action="{{ route('admin.stop-impersonation') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-3 py-2 rounded-lg text-xs font-black bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Admin
                        </button>
                    </form>
                @endif

                {{-- Quick action: new invoice (finance roles only) --}}
                @if($u->canManageFinances())
                    <a href="{{ route('dashboard.invoices.create') }}"
                       class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-black hover:bg-indigo-700 transition">
                        <i class="fas fa-plus mr-1"></i>New Invoice
                    </a>
                @endif

                {{-- User menu --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-700 font-black">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                        </div>
                        <span class="hidden md:block font-semibold">{{ $u->name }}</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                        @if($u->canManageTeam())
                            <a href="{{ route('dashboard.settings.index') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                        @endif

                        <a href="{{ route('dashboard.billing.index') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-crown mr-2"></i>Billing
                        </a>

                        <hr class="my-1">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 flex items-start">
                    <i class="fas fa-check-circle mt-0.5 mr-3"></i>
                    <div class="flex-1 text-sm font-semibold">{{ session('success') }}</div>
                    <button onclick="this.parentElement.remove()" class="text-green-400 hover:text-green-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('warning'))
    <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-900 rounded-xl p-4 flex items-start">
        <i class="fas fa-triangle-exclamation mt-0.5 mr-3"></i>
        <div class="flex-1 text-sm font-semibold">{{ session('warning') }}</div>
        <button onclick="this.parentElement.remove()" class="text-yellow-600 hover:text-yellow-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 flex items-start">
                    <i class="fas fa-exclamation-circle mt-0.5 mr-3"></i>
                    <div class="flex-1 text-sm font-semibold">{{ session('error') }}</div>
                    <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.13.3/dist/cdn.min.js" defer></script>

@stack('scripts')
</body>
</html>