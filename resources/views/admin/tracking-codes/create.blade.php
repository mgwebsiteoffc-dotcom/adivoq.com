@extends('layouts.admin')
@section('title', 'Add Tracking Code')
@section('page_title', 'Add Tracking Code')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.tracking-codes.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
        <i class="fas fa-arrow-left mr-2"></i>Back to Tracking Codes
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Add New Tracking Code</h2>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm font-semibold text-red-700 mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>Please fix the errors below:
                    </p>
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tracking-codes.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Service Selection -->
                <div>
                    <label for="service_name" class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-tag mr-2"></i>Tracking Service
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="service_name" id="service_name" 
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('service_name') border-red-500 @enderror" 
                            required onchange="updateServiceFields()">
                        <option value="">-- Select a service --</option>
                        @foreach ($services as $value => $label)
                            <option value="{{ $value }}" {{ old('service_name') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('service_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tracking ID Field -->
                <div id="tracking_id_field" style="display: none;">
                    <label for="tracking_id" class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-key mr-2"></i>Tracking ID
                    </label>
                    <input type="text" name="tracking_id" id="tracking_id" 
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('tracking_id') border-red-500 @enderror"
                           placeholder="e.g., G-XXXXXXXXXX or 123456789">
                    <p class="text-xs text-gray-500 mt-1">Your unique tracking identifier from the service</p>
                    @error('tracking_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code Field -->
                <div id="code_field" style="display: none;">
                    <label for="code" class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-code mr-2"></i>Tracking Code / Script
                    </label>
                    <textarea name="code" id="code" rows="8" 
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('code') border-red-500 @enderror"
                              placeholder="Paste your custom tracking script here"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Only used for custom tracking scripts</p>
                    @error('code')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Display Name -->
                <div>
                    <label for="display_name" class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-heading mr-2"></i>Display Name
                    </label>
                    <input type="text" name="display_name" id="display_name" 
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="e.g., Main Website Pixel" 
                           value="{{ old('display_name') }}">
                    <p class="text-xs text-gray-500 mt-1">For your reference to identify this code</p>
                </div>

                <!-- Note -->
                <div>
                    <label for="note" class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-sticky-note mr-2"></i>Note
                    </label>
                    <textarea name="note" id="note" rows="3" 
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                              placeholder="Add any notes about this tracking code">{{ old('note') }}</textarea>
                </div>

                <!-- Enable Checkbox -->
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_enabled" id="is_enabled" value="1" 
                               {{ old('is_enabled', true) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-4 peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                    </label>
                    <label for="is_enabled" class="text-sm font-medium text-gray-900">
                        Enable this tracking code immediately
                    </label>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>Add Tracking Code
                    </button>
                    <a href="{{ route('admin.tracking-codes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-blue-50 rounded-xl border border-blue-200 p-6 sticky top-20">
            <h3 class="text-sm font-semibold text-blue-900 mb-4">
                <i class="fas fa-info-circle mr-2"></i>Setup Instructions
            </h3>
            <div class="space-y-4 text-xs text-blue-800">
                <div>
                    <p class="font-semibold text-blue-900 mb-1">
                        <i class="fab fa-facebook mr-2 text-blue-600"></i>Meta Pixel
                    </p>
                    <p class="ml-5">Get your Pixel ID from Facebook Business Manager → Data Sources → Pixels</p>
                </div>

                <div>
                    <p class="font-semibold text-blue-900 mb-1">
                        <i class="fab fa-google mr-2 text-red-600"></i>Google Analytics (GA4)
                    </p>
                    <p class="ml-5">Get your Measurement ID from Google Analytics → Data Streams → Web (starts with G-)</p>
                </div>

                <div>
                    <p class="font-semibold text-blue-900 mb-1">
                        <i class="fas fa-chart-line mr-2 text-cyan-600"></i>Microsoft Clarity
                    </p>
                    <p class="ml-5">Get your Project ID from Clarity Project Settings</p>
                </div>

                <div>
                    <p class="font-semibold text-blue-900 mb-1">
                        <i class="fas fa-code mr-2 text-purple-600"></i>Custom Script
                    </p>
                    <p class="ml-5">Paste any custom tracking code. It will be injected into your website head tag.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateServiceFields() {
    const service = document.getElementById('service_name').value;
    const trackingIdField = document.getElementById('tracking_id_field');
    const codeField = document.getElementById('code_field');

    // Reset displays
    trackingIdField.style.display = 'none';
    codeField.style.display = 'none';

    if (service && service !== 'custom') {
        trackingIdField.style.display = 'block';
        document.getElementById('tracking_id').required = true;
    } else if (service === 'custom') {
        codeField.style.display = 'block';
        document.getElementById('code').required = true;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateServiceFields);
</script>
@endsection
