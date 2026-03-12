@extends('layouts.tenant')
@section('title','Settings')
@section('page_title','Settings')

@section('content')
<!-- Quick Links -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('dashboard.settings.services.index') }}" class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-6 hover:shadow-lg transition">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-sm font-black text-gray-900 mb-1">Service Catalog</h3>
                <p class="text-xs text-gray-600">Create reusable invoice services with HSN codes</p>
            </div>
            <i class="fas fa-arrow-right text-purple-600"></i>
        </div>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Profile --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Profile</h3>
        <form method="POST" action="{{ route('dashboard.settings.profile') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Your Name</label>
                    <input name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Phone</label>
                    <input name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Business Name</label>
                    <input name="business_name" value="{{ old('business_name', $tenant->business_name) }}" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Logo</label>
                    <input type="file" name="logo" class="w-full text-sm">
                </div>
            </div>

            <button class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">Save Profile</button>
        </form>
    </div>

    {{-- Invoice Settings --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Invoice Settings</h3>
        <form method="POST" action="{{ route('dashboard.settings.invoice') }}" class="space-y-3">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Invoice Prefix</label>
                    <input name="invoice_prefix" value="{{ old('invoice_prefix', $invoiceSetting?->invoice_prefix ?? 'INV') }}" class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Invoice Color</label>
                    <input name="invoice_color" value="{{ old('invoice_color', $invoiceSetting?->invoice_color ?? '#4F46E5') }}" class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Default Terms</label>
                    <select name="default_payment_terms" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                        @foreach(config('invoicehero.payment_terms') as $k=>$v)
                            <option value="{{ $k }}" @selected(old('default_payment_terms',$invoiceSetting?->default_payment_terms ?? 'net_30')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Default Terms Days</label>
                    <input type="number" name="default_payment_terms_days" value="{{ old('default_payment_terms_days',$invoiceSetting?->default_payment_terms_days ?? 30) }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm">
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="checkbox" name="show_logo" value="1" class="rounded" @checked(old('show_logo',$invoiceSetting?->show_logo ?? true))>
                    <span class="text-sm font-bold text-gray-700">Show logo on invoice</span>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Default Notes</label>
                    <textarea name="default_notes" rows="2" class="w-full px-3 py-2.5 border rounded-lg text-sm">{{ old('default_notes',$invoiceSetting?->default_notes) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Default T&C</label>
                    <textarea name="default_terms_and_conditions" rows="2" class="w-full px-3 py-2.5 border rounded-lg text-sm">{{ old('default_terms_and_conditions',$invoiceSetting?->default_terms_and_conditions) }}</textarea>
                </div>
            </div>

            <button class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">Save Invoice Settings</button>
        </form>
    </div>

<div class="bg-white rounded-xl border p-6">
    <h3 class="text-sm font-black text-gray-900 mb-4">Payment Gateway (Razorpay)</h3>

    <form method="POST" action="{{ route('dashboard.settings.payment-gateway') }}" class="space-y-3">
        @csrf @method('PUT')

        <label class="flex items-center gap-2 bg-gray-50 p-3 rounded-lg">
            <input type="checkbox" name="razorpay_enabled" value="1" class="rounded"
                   @checked(old('razorpay_enabled', $gateway?->razorpay_enabled ?? false))>
            <span class="text-sm font-bold text-gray-700">Enable Razorpay Checkout</span>
        </label>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Key ID</label>
                <input name="razorpay_key_id" value="{{ old('razorpay_key_id', $gateway?->razorpay_key_id) }}"
                       class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Key Secret</label>
                <input name="razorpay_key_secret" value="" placeholder="Enter new secret to update"
                       class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono">
                <p class="text-xs text-gray-400 mt-1">Leave blank to keep existing secret.</p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Webhook Secret</label>
            <input name="razorpay_webhook_secret" value="" placeholder="Enter webhook secret to update"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono">
            <p class="text-xs text-gray-400 mt-1">Set in Razorpay Dashboard → Settings → Webhooks.</p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 text-yellow-900 rounded-lg p-3 text-xs">
            <strong>Webhook URL:</strong>
            <span class="font-mono">{{ url('/webhooks/razorpay') }}</span><br>
            Enable events: <strong>payment.captured</strong>, <strong>payment.failed</strong>
        </div>

        <button class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">
            Save Razorpay Settings
        </button>
    </form>

    @php
    $webhookUrl = url('/webhooks/razorpay');
    $webhookConfigured = !empty($gateway?->razorpay_webhook_secret);
@endphp

<div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-4">
    <h4 class="text-sm font-black text-gray-900 mb-2">
        <i class="fas fa-plug mr-2 text-indigo-600"></i>Webhook Setup Helper
    </h4>

    <div class="text-sm text-gray-700">
        <p class="mb-2">
            <span class="font-bold">Status:</span>
            @if($webhookConfigured)
                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-black">Configured</span>
            @else
                <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-black">Not Configured</span>
            @endif
        </p>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <div class="flex-1">
                <p class="text-xs text-gray-500 font-bold uppercase">Webhook URL</p>
                <p class="font-mono text-xs break-all">{{ $webhookUrl }}</p>
            </div>
            <button type="button"
                onclick="navigator.clipboard.writeText('{{ $webhookUrl }}'); this.innerText='Copied'; setTimeout(()=>this.innerText='Copy URL', 1500)"
                class="px-4 py-2 bg-white border rounded-lg text-sm font-black hover:bg-gray-100">
                Copy URL
            </button>
        </div>

        <div class="mt-3 text-xs text-gray-600">
            <p class="font-bold mb-1">Enable these events in Razorpay:</p>
            <ul class="list-disc list-inside space-y-1">
                <li><span class="font-mono">payment.captured</span></li>
                <li><span class="font-mono">payment.failed</span></li>
            </ul>
            <p class="mt-2">
                Razorpay Dashboard → <b>Settings</b> → <b>Webhooks</b> → Add webhook URL and secret.
            </p>
        </div>

        <div class="mt-4">
            <p class="text-xs text-gray-500 font-bold uppercase mb-2">Last Webhooks Received</p>
            @if(isset($razorpayWebhookLogs) && $razorpayWebhookLogs->count())
                <div class="space-y-2">
                    @foreach($razorpayWebhookLogs as $log)
                        <div class="bg-white border rounded-lg p-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-mono">{{ $log->event }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-black
                                    {{ $log->status === 'processed' ? 'bg-green-100 text-green-700' : ($log->status === 'error' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ strtoupper($log->status) }}
                                </span>
                            </div>
                            <div class="mt-1 text-gray-500">
                                Order: <span class="font-mono">{{ $log->gateway_order_id ?? '—' }}</span>
                                • Payment: <span class="font-mono">{{ $log->gateway_payment_id ?? '—' }}</span>
                            </div>
                            <div class="mt-1 text-gray-400">
                                {{ $log->created_at->diffForHumans() }}
                                • Signature: {{ $log->signature_valid ? 'OK' : 'BAD' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-500">No webhook received yet. Use Razorpay “Send Test Webhook”.</p>
            @endif
        </div>
    </div>
</div>
</div>

    {{-- Notifications --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Notifications</h3>
        <form method="POST" action="{{ route('dashboard.settings.notifications') }}" class="space-y-3">
            @csrf @method('PUT')

            @php $n = $notifications; @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach([
                    'email_on_invoice_sent' => 'Email: Invoice Sent',
                    'email_on_payment_received' => 'Email: Payment Received',
                    'email_on_invoice_overdue' => 'Email: Invoice Overdue',
                    'whatsapp_on_invoice_sent' => 'WhatsApp: Invoice Sent',
                    'whatsapp_on_payment_received' => 'WhatsApp: Payment Received',
                    'whatsapp_on_invoice_overdue' => 'WhatsApp: Invoice Overdue',
                ] as $key => $label)
                    <label class="flex items-center gap-2 bg-gray-50 p-3 rounded-lg">
                        <input type="checkbox" name="{{ $key }}" value="1" class="rounded" @checked(old($key, $n?->$key ?? false))>
                        <span class="text-sm font-bold text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Reminder Days Before Due</label>
                    <input type="number" name="reminder_days_before_due" value="{{ old('reminder_days_before_due',$n?->reminder_days_before_due ?? 3) }}"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Reminder Frequency</label>
                    <select name="reminder_frequency" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                        @foreach(['once'=>'Once','daily'=>'Daily','weekly'=>'Weekly'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('reminder_frequency',$n?->reminder_frequency ?? 'once')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">Save Notifications</button>
        </form>
    </div>

    {{-- Exports + Password --}}
    <div class="bg-white rounded-xl border p-6 space-y-6">
        <div>
            <h3 class="text-sm font-black text-gray-900 mb-3">Data Export</h3>
            <div class="flex flex-wrap gap-2">
                <a class="px-4 py-2 bg-gray-100 rounded-lg text-sm font-bold hover:bg-gray-200" href="{{ route('dashboard.settings.export','brands') }}">Export Brands</a>
                <a class="px-4 py-2 bg-gray-100 rounded-lg text-sm font-bold hover:bg-gray-200" href="{{ route('dashboard.settings.export','invoices') }}">Export Invoices</a>
                <a class="px-4 py-2 bg-gray-100 rounded-lg text-sm font-bold hover:bg-gray-200" href="{{ route('dashboard.settings.export','payments') }}">Export Payments</a>
            </div>
        </div>

        <div class="border-t pt-5">
            <h3 class="text-sm font-black text-gray-900 mb-3">Change Password</h3>
            <form method="POST" action="{{ route('dashboard.settings.password') }}" class="space-y-3">
                @csrf @method('PUT')
                <input type="password" name="current_password" placeholder="Current password" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                <input type="password" name="password" placeholder="New password" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                <input type="password" name="password_confirmation" placeholder="Confirm new password" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                <button class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">Update Password</button>
            </form>
        </div>
    </div>
</div>
@endsection