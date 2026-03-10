@extends('layouts.tenant')
@section('title', 'Brands')
@section('page_title', 'Brand Management')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex items-end gap-3 flex-1">
        <div class="relative flex-1 max-w-xs">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search brands..."
                   class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">All</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('dashboard.brands.create') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
        <i class="fas fa-plus mr-1.5"></i>Add Brand
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($brands as $brand)
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center mr-3 flex-shrink-0">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" class="w-11 h-11 rounded-xl object-cover">
                        @else
                            <span class="text-indigo-600 font-bold text-sm">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <a href="{{ route('dashboard.brands.show', $brand) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-600 transition truncate block">{{ $brand->name }}</a>
                        <p class="text-xs text-gray-500 truncate">{{ $brand->contact_person ?? $brand->email ?? 'No contact' }}</p>
                    </div>
                </div>
                @if($brand->status === 'archived')
                    <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Archived</span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-gray-50 rounded-lg p-2.5 text-center">
                    <p class="text-lg font-bold text-gray-900">{{ $brand->campaigns_count }}</p>
                    <p class="text-xs text-gray-500">Campaigns</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-2.5 text-center">
                    <p class="text-lg font-bold text-gray-900">{{ $brand->invoices_count }}</p>
                    <p class="text-xs text-gray-500">Invoices</p>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <a href="{{ route('dashboard.brands.show', $brand) }}" class="text-xs text-indigo-600 font-medium hover:underline">View Details</a>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('dashboard.brands.edit', $brand) }}" class="p-1.5 text-gray-400 hover:text-blue-600 transition"><i class="fas fa-edit text-xs"></i></a>
                    @if($brand->status === 'active')
                        <form method="POST" action="{{ route('dashboard.brands.archive', $brand) }}" class="inline">
                            @csrf
                            <button class="p-1.5 text-gray-400 hover:text-orange-600 transition" title="Archive"><i class="fas fa-archive text-xs"></i></button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('dashboard.brands.restore', $brand) }}" class="inline">
                            @csrf
                            <button class="p-1.5 text-gray-400 hover:text-green-600 transition" title="Restore"><i class="fas fa-undo text-xs"></i></button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-16">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-building text-indigo-400 text-xl"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No brands yet</h3>
            <p class="text-sm text-gray-500 mb-4">Add your first brand to start creating invoices.</p>
            <a href="{{ route('dashboard.brands.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                <i class="fas fa-plus mr-1.5"></i>Add First Brand
            </a>
        </div>
    @endforelse
</div>

@if($brands->hasPages())
    <div class="mt-6">{{ $brands->links() }}</div>
@endif
@endsection