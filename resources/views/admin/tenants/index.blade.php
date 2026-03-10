@extends('layouts.admin')
@section('title', 'Tenants')
@section('page_title', 'Tenant Management')

@section('content')
{{-- Stats Bar --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Tenants', 'value' => $stats['total'], 'icon' => 'fa-users', 'color' => 'bg-indigo-500'],
        ['label' => 'Active', 'value' => $stats['active'], 'icon' => 'fa-check-circle', 'color' => 'bg-green-500'],
        ['label' => 'On Trial', 'value' => $stats['trial'], 'icon' => 'fa-clock', 'color' => 'bg-yellow-500'],
        ['label' => 'Suspended', 'value' => $stats['suspended'], 'icon' => 'fa-ban', 'color' => 'bg-red-500'],
    ] as $card)
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center">
            <div class="w-10 h-10 {{ $card['color'] }} rounded-lg flex items-center justify-center mr-3">
                <i class="fas {{ $card['icon'] }} text-white text-sm"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
                <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- Filters + Add Button --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-end gap-3 flex-1">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, business..."
                           class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Plan</label>
                <select name="plan" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Plans</option>
                    @foreach(['free','starter','professional','enterprise'] as $p)
                        <option value="{{ $p }}" {{ request('plan') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Plan Status</label>
                <select name="plan_status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="trial" {{ request('plan_status') === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="active" {{ request('plan_status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('plan_status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-search mr-1"></i>Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'plan', 'plan_status']))
                <a href="{{ route('admin.tenants.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                    <i class="fas fa-times mr-1"></i>Clear
                </a>
            @endif
        </form>

        <a href="{{ route('admin.tenants.create') }}" class="px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition shadow-sm whitespace-nowrap">
            <i class="fas fa-plus mr-1.5"></i>Add New Tenant
        </a>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Tenant</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Owner</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Plan</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Users</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Brands</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Invoices</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Joined</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center mr-3 flex-shrink-0">
                                    @if($tenant->logo)
                                        <img src="{{ asset('storage/' . $tenant->logo) }}" class="w-9 h-9 rounded-lg object-cover">
                                    @else
                                        <span class="text-indigo-600 font-bold text-sm">{{ strtoupper(substr($tenant->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.tenants.show', $tenant) }}" class="font-semibold text-gray-900 hover:text-indigo-600 transition truncate block">
                                        {{ $tenant->name }}
                                    </a>
                                    <p class="text-xs text-gray-500 truncate">{{ $tenant->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($tenant->owner)
                                <p class="text-sm text-gray-700">{{ $tenant->owner->name }}</p>
                                <p class="text-xs text-gray-400">{{ $tenant->owner->email }}</p>
                            @else
                                <span class="text-xs text-red-500 font-medium"><i class="fas fa-exclamation-triangle mr-1"></i>No owner</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $planBg = ['free' => 'bg-gray-100 text-gray-600', 'starter' => 'bg-blue-100 text-blue-700', 'professional' => 'bg-indigo-100 text-indigo-700', 'enterprise' => 'bg-purple-100 text-purple-700'];
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full capitalize {{ $planBg[$tenant->plan] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $tenant->plan }}
                            </span>
                            @if($tenant->plan_status === 'trial')
                                <span class="block text-xs text-orange-600 mt-0.5">
                                    Trial {{ $tenant->trial_ends_at ? '→ ' . $tenant->trial_ends_at->format('M d') : '' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full
                                {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $tenant->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 font-medium">{{ $tenant->users_count }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 font-medium">{{ $tenant->brands_count }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 font-medium">{{ $tenant->invoices_count }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                            {{ $tenant->created_at->format('M d, Y') }}
                            <br>
                            <span class="text-gray-400">{{ $tenant->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @if($tenant->owner)
                                    <a href="{{ route('admin.tenants.impersonate', $tenant) }}" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Login as Tenant"
                                       onclick="return confirm('Login as {{ $tenant->name }}? You will be logged out of admin.')">
                                        <i class="fas fa-sign-in-alt text-xs"></i>
                                    </a>
                                @endif
                                @if($tenant->status === 'active')
                                    <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" class="inline"
                                          onsubmit="return confirm('Suspend {{ $tenant->name }}? All users will be locked out.')">
                                        @csrf
                                        <button class="p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition" title="Suspend">
                                            <i class="fas fa-pause-circle text-xs"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.tenants.reactivate', $tenant) }}" class="inline">
                                        @csrf
                                        <button class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Reactivate">
                                            <i class="fas fa-play-circle text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" class="inline"
                                      onsubmit="return confirm('PERMANENTLY DELETE {{ $tenant->name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-1">No tenants found</h3>
                            <p class="text-xs text-gray-500 mb-4">
                                @if(request()->hasAny(['search', 'status', 'plan']))
                                    Try adjusting your filters.
                                @else
                                    Get started by adding your first tenant.
                                @endif
                            </p>
                            <a href="{{ route('admin.tenants.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-plus mr-1.5"></i>Add First Tenant
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tenants->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            {{ $tenants->links() }}
        </div>
    @endif
</div>

<div class="mt-4 text-xs text-gray-400 text-right">
    Showing {{ $tenants->firstItem() ?? 0 }}–{{ $tenants->lastItem() ?? 0 }} of {{ $tenants->total() }} tenants
</div>
@endsection