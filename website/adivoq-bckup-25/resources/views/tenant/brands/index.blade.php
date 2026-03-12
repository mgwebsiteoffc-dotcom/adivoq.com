@extends('layouts.tenant')

@section('title', 'Brands')
@section('page_title', 'Brands')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <form method="GET" class="w-1/3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search brands..."
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-sm text-white">
        </form>

        <a href="{{ route('tenant.brands.create') }}"
           class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
            + Add Brand
        </a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-surface-700/50 text-surface-400 text-xs uppercase">
                <tr>
                    <th class="px-6 py-4 text-left">Brand</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Invoices</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-700">
                @forelse($brands as $brand)
                <tr class="hover:bg-surface-700/30">
                    <td class="px-6 py-4 text-white">{{ $brand->name }}</td>
                    <td class="px-6 py-4 text-surface-400">{{ $brand->email }}</td>
                    <td class="px-6 py-4 text-surface-400">{{ $brand->invoices()->count() }}</td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="{{ route('tenant.brands.show', $brand) }}" class="text-brand-400 text-sm">View</a>
                        <a href="{{ route('tenant.brands.edit', $brand) }}" class="text-blue-400 text-sm">Edit</a>
                        <form action="{{ route('tenant.brands.destroy', $brand) }}"
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-400 text-sm"
                                onclick="return confirm('Delete brand?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-surface-500">
                        No brands yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $brands->links() }}
</div>
@endsection