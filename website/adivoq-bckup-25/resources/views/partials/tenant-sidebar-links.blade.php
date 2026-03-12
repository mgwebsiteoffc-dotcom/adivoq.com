<!-- resources/views/partials/tenant-sidebar-links.blade.php -->

@php
    $links = [
        ['url' => '/dashboard', 'match' => 'dashboard', 'exact' => true, 'label' => 'Dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],

        ['type' => 'header', 'label' => 'Business'],

        ['url' => '/dashboard/brands', 'match' => 'dashboard/brands*', 'label' => 'Brands', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ['url' => '/dashboard/campaigns', 'match' => 'dashboard/campaigns*', 'label' => 'Campaigns', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],

        ['type' => 'header', 'label' => 'Finance'],

        ['url' => '/dashboard/invoices', 'match' => 'dashboard/invoices*', 'label' => 'Invoices', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['url' => '/dashboard/payments', 'match' => 'dashboard/payments*', 'label' => 'Payments', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        ['url' => '/dashboard/expenses', 'match' => 'dashboard/expenses*', 'label' => 'Expenses', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['url' => '/dashboard/billing', 'match' => 'dashboard/billing*', 'label' => 'Billing', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],

        ['type' => 'header', 'label' => 'Reports'],

        ['url' => '/dashboard/reports', 'match' => 'dashboard/reports*', 'label' => 'Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ['url' => '/dashboard/tax', 'match' => 'dashboard/tax*', 'label' => 'Tax Summary', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],

        ['type' => 'header', 'label' => 'Manage'],

        ['url' => '/dashboard/team', 'match' => 'dashboard/team', 'label' => 'Team', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        [
    'url' => '/dashboard/team/revenue',
    'match' => 'dashboard/team/revenue*',
    'label' => 'Team Revenue',
    'icon' => 'M4 3h16v18l-3-2-3 2-3-2-3 2-3-2-3 2V3zm4 5h8M8 10h8M8 14h5'
],

[
    'url' => '/dashboard/team/analytics',
    'match' => 'dashboard/team/analytics*',
    'label' => 'Team Analytics',
    'icon' => 'M11 3.055A9 9 0 1021 12h-9V3.055zM13 21.95A9 9 0 0012 3v9h9a9 9 0 01-8 8.95z'
],
        ['url' => '/dashboard/settings', 'match' => 'dashboard/settings*', 'label' => 'Settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
    ];
@endphp

@foreach($links as $link)
    @if(isset($link['type']) && $link['type'] === 'header')
        <p class="px-4 pt-4 pb-1 text-xs font-semibold text-surface-500 uppercase tracking-wider">{{ $link['label'] }}</p>
    @else
        @php
            $isActive = isset($link['exact']) && $link['exact']
                ? request()->is($link['match'])
                : request()->is($link['match']);
        @endphp
        <a href="{{ url($link['url']) }}"
           class="{{ $isActive ? 'flex items-center gap-3 px-4 py-2.5 text-sm text-white rounded-lg bg-brand-600/20 border-l-2 border-brand-500' : 'flex items-center gap-3 px-4 py-2.5 text-sm text-surface-400 rounded-lg hover:text-white hover:bg-surface-800 transition-all duration-200' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
            </svg>
            {{ $link['label'] }}
        </a>
    @endif
@endforeach