@extends('layouts.tenant')
@section('title', 'Campaigns')
@section('page_title', 'Campaign Management')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search campaigns..."
               class="px-3 py-2 border border-gray-200 rounded-lg text-sm flex-1 min-w-[150px] focus:ring-2 focus:ring-indigo-500">
        <select name="brand_id" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">All Brands</option>
            @foreach($brands as $b)<option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>@endforeach
        </select>
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">All Status</option>
            @foreach(['draft','active','completed','cancelled'] as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
        </select>
        <select name="platform" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <option value="">All Platforms</option>
            @foreach(config('invoicehero.platforms') as $k => $v)<option value="{{ $k }}" {{ request('platform') === $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('dashboard.campaigns.create') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 whitespace-nowrap">
        <i class="fas fa-plus mr-1.5"></i>New Campaign
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Campaign</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Brand</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Platform</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Amount</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Milestones</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($campaigns as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('dashboard.campaigns.show', $c) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $c->name }}</a>
                            @if($c->start_date)<p class="text-xs text-gray-400">{{ $c->start_date->format('M d') }} — {{ $c->end_date?->format('M d, Y') ?? 'Ongoing' }}</p>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $c->brand->name ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">{{ config("invoicehero.platforms.{$c->platform}", $c->platform) }}</span></td>
                        <td class="px-4 py-3 text-right font-semibold">₹{{ number_format($c->total_amount) }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $c->milestones_count }}</td>
                        <td class="px-4 py-3">
                            @php $sc = ['draft' => 'gray', 'active' => 'green', 'completed' => 'blue', 'cancelled' => 'red']; @endphp
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $sc[$c->status] ?? 'gray' }}-100 text-{{ $sc[$c->status] ?? 'gray' }}-700">{{ ucfirst($c->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('dashboard.campaigns.show', $c) }}" class="p-1.5 text-gray-400 hover:text-indigo-600"><i class="fas fa-eye text-xs"></i></a>
                            <a href="{{ route('dashboard.campaigns.edit', $c) }}" class="p-1.5 text-gray-400 hover:text-blue-600"><i class="fas fa-edit text-xs"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-16 text-center text-gray-500"><i class="fas fa-bullhorn text-gray-300 text-2xl mb-2 block"></i>No campaigns yet. <a href="{{ route('dashboard.campaigns.create') }}" class="text-indigo-600 font-medium">Create one →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($campaigns->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $campaigns->links() }}</div>@endif
</div>
@endsection