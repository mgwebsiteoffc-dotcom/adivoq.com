@extends('layouts.tenant')

@section('title', 'Campaigns')
@section('page_title', 'Campaigns')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-white text-lg font-semibold">All Campaigns</h2>

        <a href="{{ route('tenant.campaigns.create') }}"
           class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
            + Add Campaign
        </a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-surface-700/50 text-surface-400 text-xs uppercase">
                <tr>
                    <th class="px-6 py-4 text-left">Title</th>
                    <th class="px-6 py-4">Brand</th>
                    <th class="px-6 py-4">Value</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-700">
                @forelse($campaigns as $campaign)
                <tr class="hover:bg-surface-700/30">
                    <td class="px-6 py-4 text-white">{{ $campaign->title }}</td>
                    <td class="px-6 py-4 text-surface-400">{{ $campaign->brand->name }}</td>
                    <td class="px-6 py-4 text-surface-400 font-mono">
                        ₹{{ number_format($campaign->total_value, 0) }}
                    </td>
                    <td class="px-6 py-4 text-surface-400">
                        {{ ucfirst($campaign->status) }}
                    </td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="{{ route('tenant.campaigns.show', $campaign) }}"
                           class="text-brand-400 text-sm">View</a>

                        <a href="{{ route('tenant.campaigns.edit', $campaign) }}"
                           class="text-blue-400 text-sm">Edit</a>

                        <form action="{{ route('tenant.campaigns.destroy', $campaign) }}"
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-400 text-sm"
                                onclick="return confirm('Delete campaign?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-surface-500">
                        No campaigns yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $campaigns->links() }}

</div>
@endsection