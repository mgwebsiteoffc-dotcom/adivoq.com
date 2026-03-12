<!-- resources/views/admin/tenants/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Create Tenant')
@section('page_title', 'Create Tenant')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center gap-2 text-surface-400 hover:text-white text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Tenants
        </a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
        <form action="{{ route('admin.tenants.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Tenant Info --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Tenant Information</h3>
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-surface-300 mb-1.5">Business Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 text-sm"
                            placeholder="Creator Studio Name">
                        @error('name')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="plan" class="block text-sm font-medium text-surface-300 mb-1.5">Plan *</label>
                            <select id="plan" name="plan" required
                                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                                <option value="free" {{ old('plan') === 'free' ? 'selected' : '' }}>Free</option>
                                <option value="pro" {{ old('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                                <option value="business" {{ old('plan') === 'business' ? 'selected' : '' }}>Business</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-surface-300 mb-1.5">Status *</label>
                            <select id="status" name="status" required
                                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="trial" {{ old('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-surface-700">

            {{-- Owner Info --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Owner Account</h3>
                <div class="space-y-4">
                    <div>
                        <label for="owner_name" class="block text-sm font-medium text-surface-300 mb-1.5">Owner Name *</label>
                        <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" required
                            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 text-sm"
                            placeholder="John Doe">
                        @error('owner_name')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="owner_email" class="block text-sm font-medium text-surface-300 mb-1.5">Owner Email *</label>
                        <input type="email" id="owner_email" name="owner_email" value="{{ old('owner_email') }}" required
                            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 text-sm"
                            placeholder="john@example.com">
                        @error('owner_email')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="owner_password" class="block text-sm font-medium text-surface-300 mb-1.5">Owner Password *</label>
                        <input type="password" id="owner_password" name="owner_password" required
                            class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 text-sm"
                            placeholder="Minimum 8 characters">
                        @error('owner_password')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
                    Create Tenant
                </button>
                <a href="{{ route('admin.tenants.index') }}" class="px-6 py-2.5 text-surface-400 hover:text-white transition-colors text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection