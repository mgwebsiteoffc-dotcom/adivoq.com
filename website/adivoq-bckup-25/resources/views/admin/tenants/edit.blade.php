<!-- resources/views/admin/tenants/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Tenant')
@section('page_title', 'Edit Tenant')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center gap-2 text-surface-400 hover:text-white text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Tenants
        </a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
        <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-surface-300 mb-1.5">Business Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" required
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 text-sm">
                @error('name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="plan" class="block text-sm font-medium text-surface-300 mb-1.5">Plan *</label>
                    <select id="plan" name="plan" required
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                        <option value="free" {{ old('plan', $tenant->plan) === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="pro" {{ old('plan', $tenant->plan) === 'pro' ? 'selected' : '' }}>Pro</option>
                        <option value="business" {{ old('plan', $tenant->plan) === 'business' ? 'selected' : '' }}>Business</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-surface-300 mb-1.5">Status *</label>
                    <select id="status" name="status" required
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                        <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="trial" {{ old('status', $tenant->status) === 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
            </div>

            {{-- Owner Info (Read Only) --}}
            <div class="bg-surface-700/30 rounded-lg p-4">
                <h4 class="text-sm font-medium text-surface-400 mb-3">Owner Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-surface-500">Name:</span>
                        <span class="text-white ml-2">{{ $tenant->owner->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-surface-500">Email:</span>
                        <span class="text-white ml-2">{{ $tenant->owner->email ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4">
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
                        Update Tenant
                    </button>
                    <a href="{{ route('admin.tenants.index') }}" class="px-6 py-2.5 text-surface-400 hover:text-white transition-colors text-sm">
                        Cancel
                    </a>
                </div>
                <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this tenant? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-red-400 hover:text-red-300 text-sm">
                        Delete Tenant
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection