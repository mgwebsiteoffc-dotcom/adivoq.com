@extends('layouts.admin')
@section('title', 'Add Tenant')
@section('page_title', 'Create New Tenant')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.tenants.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i>Back to Tenants
    </a>

    <form method="POST" action="{{ route('admin.tenants.store') }}">
        @csrf

        {{-- Tenant Details --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-base font-bold text-gray-900 mb-5 flex items-center">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-building text-indigo-600 text-sm"></i>
                </div>
                Tenant / Business Details
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tenant Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Priya Creates"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tenant Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="business@example.com"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name') }}" placeholder="Legal business name (optional)"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 99999 99999"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Plan & Status --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-base font-bold text-gray-900 mb-5 flex items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-crown text-purple-600 text-sm"></i>
                </div>
                Plan & Status
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Plan <span class="text-red-500">*</span></label>
                    <select name="plan" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['free' => 'Free (₹0)', 'starter' => 'Starter (₹499/mo)', 'professional' => 'Professional (₹999/mo)', 'enterprise' => 'Enterprise (₹2,499/mo)'] as $key => $label)
                            <option value="{{ $key }}" {{ old('plan', 'free') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Plan Status <span class="text-red-500">*</span></label>
                    <select name="plan_status" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="trial" {{ old('plan_status', 'trial') === 'trial' ? 'selected' : '' }}>Trial (14 days)</option>
                        <option value="active" {{ old('plan_status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ old('plan_status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Account Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 p-3 bg-blue-50 rounded-lg text-xs text-blue-700">
                <i class="fas fa-info-circle mr-1"></i>
                If Plan Status is "Trial", a 14-day trial period will be set automatically. Default settings (Invoice, Tax, Notification) will be created automatically.
            </div>
        </div>

        {{-- Owner Account --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-base font-bold text-gray-900 mb-5 flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user-shield text-green-600 text-sm"></i>
                </div>
                Owner Account
                <span class="ml-2 text-xs font-normal text-gray-500">(Login credentials for the tenant)</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Owner Name <span class="text-red-500">*</span></label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}" required placeholder="Full name"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 @error('owner_name') border-red-500 @enderror">
                    @error('owner_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Owner Email <span class="text-red-500">*</span></label>
                    <input type="email" name="owner_email" value="{{ old('owner_email') }}" required placeholder="owner@example.com"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 @error('owner_email') border-red-500 @enderror">
                    @error('owner_email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">This email will be used to login to the tenant dashboard.</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="text" name="owner_password" value="{{ old('owner_password', Str::random(12)) }}" required
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 @error('owner_password') border-red-500 @enderror">
                    @error('owner_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                        A random password has been generated. Copy it before saving — it won't be visible later.
                    </p>
                </div>
            </div>
        </div>

        {{-- Summary & Submit --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3 mt-0.5">
                    <i class="fas fa-clipboard-check text-indigo-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-gray-900 mb-2">What will be created</h3>
                    <ul class="text-sm text-gray-600 space-y-1.5">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2 text-xs"></i>Tenant account with selected plan</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2 text-xs"></i>Owner user account (login credentials)</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2 text-xs"></i>Default invoice settings (prefix: INV, Net 30)</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2 text-xs"></i>Default tax settings (GST 18%, TDS 10%)</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2 text-xs"></i>Default notification settings</li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-100 gap-3">
                <a href="{{ route('admin.tenants.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                    <i class="fas fa-plus mr-1"></i>Create Tenant & Owner Account
                </button>
            </div>
        </div>
    </form>
</div>
@endsection