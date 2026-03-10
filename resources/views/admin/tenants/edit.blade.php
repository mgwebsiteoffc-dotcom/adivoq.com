@extends('layouts.admin')
@section('title', 'Edit: ' . $tenant->name)
@section('page_title', 'Edit Tenant')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i>Back to {{ $tenant->name }}
        </a>
        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full
            {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $tenant->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
            {{ ucfirst($tenant->status) }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
        @csrf @method('PUT')

        {{-- Tenant Details --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-base font-bold text-gray-900 mb-5 flex items-center">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-building text-indigo-600 text-sm"></i>
                </div>
                Tenant Details
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tenant Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tenant Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}" required
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $tenant->business_name) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}"
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
                        @foreach(['free' => 'Free', 'starter' => 'Starter (₹499)', 'professional' => 'Professional (₹999)', 'enterprise' => 'Enterprise (₹2,499)'] as $key => $label)
                            <option value="{{ $key }}" {{ old('plan', $tenant->plan) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Plan Status <span class="text-red-500">*</span></label>
                    <select name="plan_status" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['trial','active','suspended','cancelled'] as $ps)
                            <option value="{{ $ps }}" {{ old('plan_status', $tenant->plan_status) === $ps ? 'selected' : '' }}>{{ ucfirst($ps) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Account Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
            </div>

            {{-- Info --}}
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600">
                <div><span class="font-semibold text-gray-700">Created:</span> {{ $tenant->created_at->format('M d, Y h:i A') }}</div>
                <div><span class="font-semibold text-gray-700">Trial Ends:</span> {{ $tenant->trial_ends_at?->format('M d, Y') ?? 'N/A' }}</div>
                <div><span class="font-semibold text-gray-700">Slug:</span> <code class="font-mono">{{ $tenant->slug }}</code></div>
            </div>
        </div>

        {{-- Owner Info (Read-only) --}}
        @if($tenant->owner)
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-shield text-green-600 text-sm"></i>
                    </div>
                    Owner Account
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 mb-0.5">Name</span>
                        <span class="text-gray-900 font-medium">{{ $tenant->owner->name }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 mb-0.5">Email</span>
                        <span class="text-gray-900 font-medium">{{ $tenant->owner->email }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 mb-0.5">Last Login</span>
                        <span class="text-gray-900 font-medium">{{ $tenant->owner->last_login_at?->diffForHumans() ?? 'Never' }}</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex items-center justify-between">
            <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}"
                  onsubmit="return confirm('PERMANENTLY DELETE {{ $tenant->name }} and ALL its data? This action CANNOT be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-1.5"></i>Delete Tenant
                </button>
            </form>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.tenants.show', $tenant) }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                    <i class="fas fa-save mr-1.5"></i>Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection