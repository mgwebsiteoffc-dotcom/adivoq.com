@extends('layouts.admin')
@section('title', 'Settings')
@section('page_title', 'System Settings')

@section('content')
<div class="max-w-6xl">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Settings Form -->
        <div class="lg:col-span-2 space-y-6">
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
                    <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">SMTP Port</label>
                    <input type="text" name="smtp_port" value="{{ $settings['smtp_port'] ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">SMTP Username</label>
                    <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">From Address</label>
                    <input type="email" name="from_address" value="{{ $settings['from_address'] ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- WhatsApp --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4"><i class="fab fa-whatsapp mr-2 text-green-500"></i>WhatsApp API (Whatify)</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">API Key</label>
                    <input type="text" name="whatify_api_key" value="{{ $settings['whatify_api_key'] ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Get your bearer token from whatify.in dashboard</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Base URL</label>
                    <input type="text" name="whatify_base_url" value="{{ $settings['whatify_base_url'] ?? 'https://whatify.in' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Default: https://whatify.in</p>
                </div>
                <div class="pt-3 border-t border-gray-200">
                    <button type="button" onclick="openWhatsAppTestModal()" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                        <i class="fas fa-paper-plane mr-2"></i>Send Test Message
                    </button>
                    <p class="text-xs text-gray-500 mt-2">Send a test message to verify WhatsApp integration is working</p>
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

        <!-- Sidebar - Quick Access -->
        <div class="space-y-6">
            {{-- Tracking & Analytics --}}
            <div>
                <h3 class="text-xs font-bold text-gray-600 uppercase mb-3">Modules</h3>
                <a href="{{ route('admin.tracking-codes.index') }}" class="block bg-white rounded-xl border border-gray-200 p-5 hover:shadow-lg hover:border-indigo-300 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-globe text-indigo-600 text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Tracking Codes</h4>
                            <p class="text-xs text-gray-600 mt-1">Meta Pixel, Google Analytics, Clarity</p>
                            <div class="mt-3 flex gap-2">
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">
                                    {{ \App\Models\TrackingCode::where('is_enabled', true)->count() }} Active
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- System Info --}}
            <div>
                <h3 class="text-xs font-bold text-gray-600 uppercase mb-3">System</h3>
                <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
                    <div>
                        <p class="text-xs text-gray-600">Laravel Version</p>
                        <p class="text-sm font-semibold text-gray-900">{{ \Illuminate\Foundation\Application::VERSION }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">PHP Version</p>
                        <p class="text-sm font-semibold text-gray-900">{{ phpversion() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Environment</p>
                        <p class="text-sm font-semibold text-gray-900 capitalize">{{ config('app.env') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Test Modal -->
<div id="whatsappTestModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Test WhatsApp Integration</h3>
            <button onclick="closeWhatsAppTestModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="whatsappTestForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Test Phone Number</label>
                <input type="tel" id="testPhone" name="test_phone" placeholder="+916868686868" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500" required>
                <p class="text-xs text-gray-500 mt-2">
                    <strong>Format:</strong> Use E.164 format with country code<br>
                    <strong>Examples:</strong>
                    <br>• India: <code class="bg-gray-100 px-1 rounded">+916868686868</code>
                    <br>• US: <code class="bg-gray-100 px-1 rounded">+19680825846</code>
                    <br>• Or use: <code class="bg-gray-100 px-1 rounded">+1 (968) 082-5846</code>
                    <br>• 10-digit India: <code class="bg-gray-100 px-1 rounded">9876543210</code> (auto-adds +91)
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Test Message</label>
                <textarea id="testMessage" name="test_message" rows="3" placeholder="Hello! This is a test message from InvoiceHero." class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">Hello! This is a test message from InvoiceHero.</textarea>
            </div>

            <div id="testResult" class="hidden p-3 rounded-lg text-sm"></div>

            <div class="flex gap-3">
                <button type="submit" id="sendTestBtn" class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                    <i class="fas fa-paper-plane mr-2"></i>Send Test
                </button>
                <button type="button" onclick="closeWhatsAppTestModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openWhatsAppTestModal() {
    document.getElementById('whatsappTestModal').classList.remove('hidden');
    document.getElementById('testResult').classList.add('hidden');
}

function closeWhatsAppTestModal() {
    document.getElementById('whatsappTestModal').classList.add('hidden');
    document.getElementById('whatsappTestForm').reset();
}

document.getElementById('whatsappTestForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const phone = document.getElementById('testPhone').value;
    const message = document.getElementById('testMessage').value;
    const resultDiv = document.getElementById('testResult');
    const sendBtn = document.getElementById('sendTestBtn');

    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';

    try {
        const response = await fetch('{{ route("admin.settings.test-whatsapp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({ phone, message })
        });

        const data = await response.json();

        resultDiv.classList.remove('hidden');
        resultDiv.classList.remove('bg-red-50', 'text-red-700', 'border', 'border-red-200');
        resultDiv.classList.remove('bg-green-50', 'text-green-700', 'border', 'border-green-200');
        resultDiv.classList.remove('bg-yellow-50', 'text-yellow-700', 'border', 'border-yellow-200');

        if (data.success) {
            resultDiv.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
            resultDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i><strong>✓ Success!</strong> Message sent to ' + phone;
            setTimeout(() => {
                closeWhatsAppTestModal();
            }, 2000);
        } else if (data.status === 404) {
            resultDiv.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
            resultDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i><strong>404 Error:</strong> Endpoint not found. Please check:<br>' +
                '• Base URL is correct (https://whatify.in)<br>' +
                '• API Key is valid<br>' +
                '• Check the Whatify dashboard settings';
        } else {
            resultDiv.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
            let errorMsg = data.error || 'Unknown error';
            
            // Check if there's a response with more details
            if (data.response && typeof data.response === 'object') {
                const respMsg = data.response.message || data.response.error || JSON.stringify(data.response);
                if (respMsg) {
                    errorMsg = respMsg;
                }
            }
            
            resultDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i><strong>Failed (Status ' + (data.status || 'unknown') + '):</strong> ' + errorMsg;
        }
    } catch (error) {
        resultDiv.classList.remove('hidden');
        resultDiv.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
        resultDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i><strong>Error:</strong> ' + error.message;
    } finally {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Test';
    }
});

// Close modal when clicking outside
document.getElementById('whatsappTestModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'whatsappTestModal') {
        closeWhatsAppTestModal();
    }
});
</script>
@endsection
