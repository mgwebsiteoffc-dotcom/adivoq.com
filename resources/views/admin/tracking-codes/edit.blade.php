@extends('layouts.admin')
@section('title', 'Edit Tracking Code')
@section('page_title', 'Edit Tracking Code')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.tracking-codes.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
        <i class="fas fa-arrow-left mr-2"></i>Back to Tracking Codes
    </a>
</div>

@if (session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl border border-gray-200 p-6 max-w-2xl">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Edit Tracking Code</h2>
            <p class="text-sm text-gray-500 mt-1">
                @if ($trackingCode->service_name === 'meta_pixel')
                    <i class="fab fa-facebook text-blue-600 mr-2"></i>Meta Pixel
                @elseif ($trackingCode->service_name === 'google_analytics')
                    <i class="fab fa-google text-red-600 mr-2"></i>Google Analytics
                @elseif ($trackingCode->service_name === 'clarity')
                    <i class="fas fa-chart-line text-cyan-600 mr-2"></i>Clarity
                @else
                    <i class="fas fa-code text-purple-600 mr-2"></i>Custom Script
                @endif
            </p>
        </div>
        <div class="text-xs text-gray-500 text-right">
            <p>Created: {{ $trackingCode->created_at->format('M d, Y H:i') }}</p>
            <p>Updated: {{ $trackingCode->updated_at->format('M d, Y H:i') }}</p>
        </div>
    </div>

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

    <form action="{{ route('admin.tracking-codes.update', $trackingCode) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        <!-- Service Selection (Disabled) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <i class="fas fa-tag mr-2"></i>Tracking Service
            </label>
            <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                @foreach ($services as $value => $label)
                    @if ($trackingCode->service_name === $value)
                        {{ $label }}
                    @endif
                @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-1">Cannot be changed after creation</p>
        </div>

        <!-- Tracking ID or Code -->
        @if ($trackingCode->service_name !== 'custom')
            <div>
                <label for="tracking_id" class="block text-sm font-semibold text-gray-900 mb-2">
                    <i class="fas fa-key mr-2"></i>Tracking ID
                </label>
                <input type="text" name="tracking_id" id="tracking_id" 
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('tracking_id') border-red-500 @enderror"
                       value="{{ old('tracking_id', $trackingCode->tracking_id) }}">
                @error('tracking_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        @else
            <div>
                <label for="code" class="block text-sm font-semibold text-gray-900 mb-2">
                    <i class="fas fa-code mr-2"></i>Tracking Code / Script
                </label>
                <textarea name="code" id="code" rows="8" 
                          class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('code') border-red-500 @enderror">{{ old('code', $trackingCode->code) }}</textarea>
                @error('code')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <!-- Display Name -->
        <div>
            <label for="display_name" class="block text-sm font-semibold text-gray-900 mb-2">
                <i class="fas fa-heading mr-2"></i>Display Name
            </label>
            <input type="text" name="display_name" id="display_name" 
                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   value="{{ old('display_name', $trackingCode->display_name) }}">
            <p class="text-xs text-gray-500 mt-1">Optional display name for reference</p>
        </div>

        <!-- Note -->
        <div>
            <label for="note" class="block text-sm font-semibold text-gray-900 mb-2">
                <i class="fas fa-sticky-note mr-2"></i>Note
            </label>
            <textarea name="note" id="note" rows="3" 
                      class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('note', $trackingCode->note) }}</textarea>
        </div>

        <!-- Enable Checkbox -->
        <div class="flex items-center gap-3">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_enabled" id="is_enabled" value="1" 
                       {{ old('is_enabled', $trackingCode->is_enabled) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="w-9 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-4 peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
            </label>
            <label for="is_enabled" class="text-sm font-medium text-gray-900">
                Enable this tracking code
            </label>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
            <a href="{{ route('admin.tracking-codes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <form action="{{ route('admin.tracking-codes.destroy', $trackingCode) }}" method="POST" class="inline ml-auto"
                  onsubmit="return confirm('Delete this tracking code? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </form>
        </div>
    </form>
</div>
@endsection
