@extends('layouts.tenant')

@section('title', $campaign->title)
@section('page_title', $campaign->title)

@section('content')
<div class="space-y-6">

    <a href="{{ route('tenant.campaigns.index') }}"
       class="text-surface-400 hover:text-white text-sm">
        ← Back to Campaigns
    </a>

    {{-- Campaign Card --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">

        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-white">
                    {{ $campaign->title }}
                </h2>
                <p class="text-surface-400 text-sm mt-1">
                    Brand: {{ $campaign->brand->name }}
                </p>
            </div>

            <div class="text-right">
                <p class="text-surface-400 text-sm">Deal Value</p>
                <p class="text-white font-mono text-lg">
                    ₹{{ number_format($campaign->total_value, 0) }}
                </p>
            </div>
        </div>

        {{-- Progress --}}
        @php
            $total = $campaign->milestones->count();
            $completed = $campaign->milestones->where('status','completed')->count();
            $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        @endphp

        <div class="mt-6">
            <div class="flex justify-between text-sm text-surface-400 mb-1">
                <span>Progress</span>
                <span>{{ $progress }}%</span>
            </div>
            <div class="w-full bg-surface-700 rounded-full h-2">
                <div class="bg-brand-500 h-2 rounded-full"
                     style="width: {{ $progress }}%"></div>
            </div>
        </div>

    </div>

    {{-- Milestones Section --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">

        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white">Milestones</h3>
        </div>

        {{-- Add Milestone --}}
        <form method="POST"
              action="{{ route('tenant.campaigns.milestones.store', $campaign) }}"
              class="grid md:grid-cols-4 gap-4">
            @csrf

            <input type="text"
                   name="title"
                   placeholder="Milestone title"
                   required
                   class="bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-white text-sm">

            <input type="number"
                   name="amount"
                   placeholder="Amount"
                   required
                   class="bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-white text-sm">

            <input type="date"
                   name="due_date"
                   class="bg-surface-900 border border-surface-700 rounded-lg px-3 py-2 text-white text-sm">

            <button type="submit"
                    class="bg-brand-600 text-white rounded-lg text-sm px-4 py-2 hover:bg-brand-700">
                Add
            </button>
        </form>

        {{-- List Milestones --}}
        <div class="space-y-4">

            @forelse($campaign->milestones as $milestone)
            <div class="flex justify-between items-center bg-surface-700/30 p-4 rounded-lg">

                <div>
                    <p class="text-white font-medium">
                        {{ $milestone->title }}
                    </p>
                    <p class="text-surface-400 text-xs">
                        ₹{{ number_format($milestone->amount, 0) }}
                        @if($milestone->due_date)
                            • Due {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                        @endif
                    </p>
                </div>

                <div class="flex gap-2 items-center">

                    @if($milestone->status !== 'completed')
                    <form method="POST"
                          action="{{ route('tenant.campaigns.milestones.complete', [$campaign, $milestone]) }}">
                        @csrf
                        <button class="text-green-400 text-sm">
                            Complete
                        </button>
                    </form>
                    @else
                        <span class="text-green-400 text-sm">Completed</span>
                    @endif

                    <form method="POST"
                          action="{{ route('tenant.campaigns.milestones.destroy', [$campaign, $milestone]) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-400 text-sm"
                                onclick="return confirm('Delete milestone?')">
                            Delete
                        </button>
                    </form>

                </div>

            </div>
            @empty
            <p class="text-surface-500 text-sm">No milestones yet.</p>
            @endforelse

        </div>

    </div>

</div>
@endsection