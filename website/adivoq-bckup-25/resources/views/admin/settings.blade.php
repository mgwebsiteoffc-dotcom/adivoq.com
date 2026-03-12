<!-- resources/views/admin/settings.blade.php -->
@extends('layouts.admin')

@section('title', 'Settings')
@section('page_title', 'System Settings')

@section('content')
<div class="max-w-3xl space-y-6">
    {{-- Site Info --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-6">Site Information</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-surface-300 mb-1.5">Site Name</label>
                <input type="text" value="AdivoQ" disabled
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-400 text-sm cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-300 mb-1.5">Site URL</label>
                <input type="text" value="{{ config('app.url') }}" disabled
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-400 text-sm cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-300 mb-1.5">Version</label>
                <input type="text" value="{{ config('adivoq.version', '1.0.0') }}" disabled
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-400 text-sm cursor-not-allowed">
            </div>
        </div>
    </div>

    {{-- Environment Info --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-6">Environment</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="bg-surface-700/30 rounded-lg p-4">
                <p class="text-surface-400 text-xs uppercase tracking-wider mb-1">Environment</p>
                <p class="text-white font-medium">{{ ucfirst(config('app.env')) }}</p>
            </div>
            <div class="bg-surface-700/30 rounded-lg p-4">
                <p class="text-surface-400 text-xs uppercase tracking-wider mb-1">Debug Mode</p>
                <p class="text-white font-medium">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</p>
            </div>
            <div class="bg-surface-700/30 rounded-lg p-4">
                <p class="text-surface-400 text-xs uppercase tracking-wider mb-1">PHP Version</p>
                <p class="text-white font-medium">{{ phpversion() }}</p>
            </div>
            <div class="bg-surface-700/30 rounded-lg p-4">
                <p class="text-surface-400 text-xs uppercase tracking-wider mb-1">Laravel Version</p>
                <p class="text-white font-medium">{{ app()->version() }}</p>
            </div>
        </div>
    </div>

    {{-- Default Settings --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-6">Default Settings</h3>
        <div class="space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1.5">Default Currency</label>
                    <input type="text" value="{{ config('adivoq.currency.default') }}" disabled
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-400 text-sm cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1.5">Default GST Rate</label>
                    <input type="text" value="{{ config('adivoq.tax.default_gst_rate') }}%" disabled
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-400 text-sm cursor-not-allowed">
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1.5">Default TDS Rate</label>
                    <input type="text" value="{{ config('adivoq.tax.default_tds_rate') }}%" disabled
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-400 text-sm cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1.5">Invoice Prefix</label>
                    <input type="text" value="{{ config('adivoq.invoice.default_prefix') }}" disabled
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-400 text-sm cursor-not-allowed">
                </div>
            </div>
        </div>
        <p class="text-surface-500 text-xs mt-4">These settings are configured in the environment. Contact developer to modify.</p>
    </div>

    {{-- Cache Actions --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-6">Maintenance</h3>
        <div class="flex flex-wrap gap-3">
            <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-surface-700 text-white font-medium rounded-lg hover:bg-surface-600 transition-colors text-sm">
                    Clear Cache
                </button>
            </form>
            <a href="{{ route('admin.logs.index') }}" class="px-4 py-2 bg-surface-700 text-white font-medium rounded-lg hover:bg-surface-600 transition-colors text-sm">
                View Logs
            </a>
        </div>
    </div>
</div>
@endsection