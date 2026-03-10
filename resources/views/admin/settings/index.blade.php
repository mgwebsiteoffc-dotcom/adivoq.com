@extends('layouts.admin')
@section('title', 'Settings')
@section('page_title', 'System Settings')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')

        {{-- General --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4"><i class="fas fa-cog mr-2 text-gray-400"></i>General Settings</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Site Name</label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Default Currency</label>
                        <select name="default_currency" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach(config('invoicehero.currencies') as $code => $info)
                                <option value="{{ $code }}" {{ $settings['default_currency'] === $code ? 'selected' : '' }}>{{ $info['symbol'] }} {{ $info['name'] }} ({{ $code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Default GST Rate (%)</label>
                        <select name="default_gst_rate" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach(config('invoicehero.gst_rates') as $rate)
                                <option value="{{ $rate }}" {{ $settings['default_gst_rate'] == $rate ? 'selected' : '' }}>{{ $rate }}%</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Email --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4"><i class="fas fa-envelope mr-2 text-gray-400"></i>Email Settings (SMTP)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">SMTP Host</label>
                    <input type="text" name="smtp_host" value="{{ config('mail.mailers.smtp.host') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">SMTP Port</label>
                    <input type="text" name="smtp_port" value="{{ config('mail.mailers.smtp.port') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">SMTP Username</label>
                    <input type="text" name="smtp_username" value="{{ config('mail.mailers.smtp.username') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">From Address</label>
                    <input type="email" name="from_address" value="{{ config('mail.from.address') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- WhatsApp --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4"><i class="fab fa-whatsapp mr-2 text-green-500"></i>WhatsApp API (Whatify)</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">API Key</label>
                    <input type="text" name="whatify_api_key" value="{{ config('services.whatify.api_key') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Base URL</label>
                    <input type="text" name="whatify_base_url" value="{{ config('services.whatify.base_url') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Maintenance --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4"><i class="fas fa-tools mr-2 text-gray-400"></i>Maintenance</h3>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Maintenance Mode</p>
                    <p class="text-xs text-gray-500">When enabled, only admins can access the site.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="maintenance_mode" value="1" {{ $settings['maintenance_mode'] ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                </label>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                <i class="fas fa-save mr-1"></i>Save All Settings
            </button>
        </div>
    </form>
</div>
@endsection