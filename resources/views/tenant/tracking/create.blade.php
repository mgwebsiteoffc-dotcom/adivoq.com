@extends('layouts.tenant')
@section('title', 'Create Tracking Key')
@section('page_title', 'New Tracking Key')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('dashboard.tracking.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Create New Tracking Key</h3>
            <p class="text-sm text-gray-600 mt-1">Set up a new tracking pixel and key to monitor activity</p>
        </div>

        <form method="POST" action="{{ route('dashboard.tracking.store') }}" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Key Name *</label>
                <input type="text" name="name" id="name" required 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="e.g., Invoice Page Visits" value="{{ old('name') }}">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-semibold text-gray-900 mb-2">Tracking Type *</label>
                <select name="type" id="type" required 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a type</option>
                    <option value="pixel" {{ old('type') == 'pixel' ? 'selected' : '' }}>Pixel (1x1 GIF)</option>
                    <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>Event (JSON POST)</option>
                    <option value="both" {{ old('type') == 'both' ? 'selected' : '' }}>Both (Pixel + JSON)</option>
                </select>
                @error('type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="brand_id" class="block text-sm font-semibold text-gray-900 mb-2">Associated Brand (Optional)</label>
                <select name="brand_id" id="brand_id"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- No Brand --</option>
                    @foreach(\App\Models\Brand::where('tenant_id', auth()->user()->tenant_id)->get() as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
                @error('brand_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="monthly_limit" class="block text-sm font-semibold text-gray-900 mb-2">Monthly Event Limit *</label>
                <input type="number" name="monthly_limit" id="monthly_limit" required 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="e.g., 10000" value="{{ old('monthly_limit', 10000) }}" min="100">
                @error('monthly_limit')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="is_active" class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-gray-900">Active</span>
                </label>
                @error('is_active')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-4 border-t border-gray-200 flex gap-3 justify-end">
                <a href="{{ route('dashboard.tracking.index') }}" 
                    class="px-5 py-2.5 text-gray-700 font-medium border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                    Create Tracking Key
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
