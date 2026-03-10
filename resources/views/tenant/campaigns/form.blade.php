@extends('layouts.tenant')
@section('title', $campaign ? 'Edit Campaign' : 'New Campaign')
@section('page_title', $campaign ? 'Edit Campaign' : 'Create Campaign')

@section('content')
<div class="max-w-2xl">
    <a href="{{ $campaign ? route('dashboard.campaigns.show', $campaign) : route('dashboard.campaigns.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i>Back</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ $campaign ? route('dashboard.campaigns.update', $campaign) : route('dashboard.campaigns.store') }}">
            @csrf
            @if($campaign) @method('PUT') @endif

            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Campaign Name *</label>
                        <input type="text" name="name" value="{{ old('name', $campaign?->name) }}" required placeholder="e.g., Summer Collection Launch"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Brand *</label>
                        <select name="brand_id" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Brand</option>
                            @foreach($brands as $b)<option value="{{ $b->id }}" {{ old('brand_id', $campaign?->brand_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Platform *</label>
                        <select name="platform" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach($platforms as $k => $v)<option value="{{ $k }}" {{ old('platform', $campaign?->platform) === $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Campaign Type *</label>
                        <select name="campaign_type" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach(['sponsored_post' => 'Sponsored Post', 'brand_deal' => 'Brand Deal', 'affiliate' => 'Affiliate', 'collaboration' => 'Collaboration', 'other' => 'Other'] as $k => $v)
                                <option value="{{ $k }}" {{ old('campaign_type', $campaign?->campaign_type ?? 'brand_deal') === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Total Deal Amount (₹) *</label>
                        <input type="number" name="total_amount" value="{{ old('total_amount', $campaign?->total_amount) }}" required step="0.01" min="0"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $campaign?->start_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $campaign?->end_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach(['draft' => 'Draft', 'active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $k => $v)
                                @if(!$campaign && in_array($k, ['completed','cancelled'])) @continue @endif
                                <option value="{{ $k }}" {{ old('status', $campaign?->status ?? 'draft') === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description', $campaign?->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('notes', $campaign?->notes) }}</textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('dashboard.campaigns.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-save mr-1"></i>{{ $campaign ? 'Update' : 'Create' }} Campaign
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection