@extends('layouts.tenant')
@section('title', $campaign->name)
@section('page_title', $campaign->name)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center space-x-3">
        <a href="{{ route('dashboard.campaigns.index') }}" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        @php $sc = ['draft' => 'gray', 'active' => 'green', 'completed' => 'blue', 'cancelled' => 'red']; @endphp
        <span class="px-3 py-1 text-xs font-bold rounded-full bg-{{ $sc[$campaign->status] }}-100 text-{{ $sc[$campaign->status] }}-700">{{ ucfirst($campaign->status) }}</span>
        <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">{{ config("invoicehero.platforms.{$campaign->platform}") }}</span>
    </div>
    <div class="flex items-center space-x-2">
        @if($campaign->status === 'active')
            <form method="POST" action="{{ route('dashboard.campaigns.complete', $campaign) }}" onsubmit="return confirm('Mark as completed?')">@csrf
                <button class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700"><i class="fas fa-check mr-1"></i>Complete</button>
            </form>
        @endif
        <a href="{{ route('dashboard.campaigns.edit', $campaign) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700"><i class="fas fa-edit mr-1"></i>Edit</a>
    </div>
</div>

{{-- Stats + Progress --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-xl font-bold text-gray-900">₹{{ number_format($campaign->total_amount) }}</p>
        <p class="text-xs text-gray-500">Deal Amount</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-xl font-bold text-green-600">₹{{ number_format($revenueCollected) }}</p>
        <p class="text-xs text-gray-500">Collected</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-xl font-bold text-gray-900">{{ $completedMilestones }}/{{ $totalMilestones }}</p>
        <p class="text-xs text-gray-500">Milestones Done</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-xl font-bold text-gray-900">{{ $campaign->invoices->count() }}</p>
        <p class="text-xs text-gray-500">Invoices</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-xl font-bold text-red-600">₹{{ number_format($totalExpenses) }}</p>
        <p class="text-xs text-gray-500">Expenses</p>
    </div>
</div>

{{-- Revenue Progress Bar --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-semibold text-gray-700">Revenue Progress</span>
        <span class="text-sm font-bold text-indigo-600">{{ $revenueProgress }}%</span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-3">
        <div class="bg-gradient-to-r from-indigo-500 to-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $revenueProgress }}%"></div>
    </div>
    <p class="text-xs text-gray-500 mt-2">₹{{ number_format($revenueCollected) }} of ₹{{ number_format($campaign->total_amount) }} collected</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Milestones --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Milestones ({{ $totalMilestones }})</h3>
        </div>

        <div class="space-y-3 mb-6">
            @forelse($campaign->milestones as $ms)
                <div class="flex items-start py-3 border-b border-gray-50 last:border-0" x-data="{ editing: false }">
                    <div x-show="!editing" class="flex items-start w-full">
                        @if($ms->status === 'completed')
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <i class="fas fa-check text-green-600 text-xs"></i>
                            </div>
                        @else
                            <form method="POST" action="{{ route('dashboard.milestones.complete', $ms) }}" class="mr-3 mt-0.5">
                                @csrf
                                <button type="submit" class="w-6 h-6 border-2 border-gray-300 rounded-full hover:border-green-500 transition flex-shrink-0" title="Mark Complete"></button>
                            </form>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium {{ $ms->status === 'completed' ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ $ms->title }}</p>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                @if($ms->amount > 0)<span>₹{{ number_format($ms->amount) }}</span>@endif
                                @if($ms->due_date)<span>Due: {{ $ms->due_date->format('M d') }}</span>@endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-1 ml-2">
                            <button @click="editing = true" class="p-1 text-gray-400 hover:text-blue-600"><i class="fas fa-edit text-xs"></i></button>
                            <form method="POST" action="{{ route('dashboard.milestones.destroy', $ms) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="p-1 text-gray-400 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit Form --}}
                    <div x-show="editing" x-cloak class="w-full">
                        <form method="POST" action="{{ route('dashboard.milestones.update', $ms) }}">
                            @csrf @method('PUT')
                            <div class="space-y-2">
                                <input type="text" name="title" value="{{ $ms->title }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" name="amount" value="{{ $ms->amount }}" step="0.01" placeholder="Amount" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <input type="date" name="due_date" value="{{ $ms->due_date?->format('Y-m-d') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                                <div class="flex space-x-2">
                                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg">Save</button>
                                    <button type="button" @click="editing = false" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs rounded-lg">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No milestones yet.</p>
            @endforelse
        </div>

        {{-- Add Milestone --}}
        <div class="border-t border-gray-200 pt-4">
            <form method="POST" action="{{ route('dashboard.milestones.store', $campaign) }}">
                @csrf
                <div class="space-y-2">
                    <input type="text" name="title" placeholder="Milestone title *" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="amount" placeholder="Amount (₹)" step="0.01" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <input type="date" name="due_date" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-1"></i>Add Milestone
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Linked Invoices --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Invoices ({{ $campaign->invoices->count() }})</h3>
            <a href="{{ route('dashboard.invoices.create', ['campaign_id' => $campaign->id, 'brand_id' => $campaign->brand_id]) }}" class="text-xs text-indigo-600 font-medium hover:underline">
                <i class="fas fa-plus mr-1"></i>New Invoice
            </a>
        </div>
        <div class="space-y-3">
            @forelse($campaign->invoices as $inv)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <a href="{{ route('dashboard.invoices.show', $inv) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $inv->invoice_number }}</a>
                        <p class="text-xs text-gray-500">{{ $inv->issue_date->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold">₹{{ number_format($inv->total_amount) }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $inv->status === 'paid' ? 'green' : ($inv->status === 'draft' ? 'gray' : 'blue') }}-100 text-{{ $inv->status === 'paid' ? 'green' : ($inv->status === 'draft' ? 'gray' : 'blue') }}-700">{{ ucfirst($inv->status) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-6">No invoices linked to this campaign.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection