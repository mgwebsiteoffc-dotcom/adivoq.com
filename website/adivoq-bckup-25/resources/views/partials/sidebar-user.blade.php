<!-- resources/views/partials/sidebar-user.blade.php -->

<div class="border-t border-surface-800 p-4">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-brand-600 rounded-full flex items-center justify-center flex-shrink-0">
            <span class="text-white text-xs font-semibold">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-surface-200 truncate">{{ auth()->user()->name ?? 'User' }}</p>
            <p class="text-xs text-surface-500 truncate">{{ auth()->user()->email ?? '' }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-surface-500 hover:text-red-400 transition-colors" title="Logout">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>
</div>