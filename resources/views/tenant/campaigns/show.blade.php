@extends('layouts.tenant')
@section('title', $campaign->name)
@section('page_title', $campaign->name)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center space-x-3">
        <a href="{{ route('dashboard.campaigns.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i>Back
        </a>
        @php $sc = ['draft' => 'gray', 'active' => 'green', 'completed' => 'blue', 'cancelled' => 'red']; @endphp
        <span class="px-3 py-1 text-xs font-bold rounded-full bg-{{ $sc[$campaign->status] ?? 'gray' }}-100 text-{{ $sc[$campaign->status] ?? 'gray' }}-700">
            {{ ucfirst($campaign->status) }}
        </span>
        <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
            {{ config("invoicehero.platforms.{$campaign->platform}") }}
        </span>
    </div>

    <div class="flex items-center space-x-2">
        @if($campaign->status === 'active')
            <form method="POST" action="{{ route('dashboard.campaigns.complete', $campaign) }}" onsubmit="return confirm('Mark as completed?')">
                @csrf
                <button class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700">
                    <i class="fas fa-check mr-1"></i>Complete
                </button>
            </form>
        @endif

        <a href="{{ route('dashboard.campaigns.edit', $campaign) }}"
           class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700">
            <i class="fas fa-edit mr-1"></i>Edit
        </a>
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
        <div class="bg-gradient-to-r from-indigo-500 to-green-500 h-3 rounded-full transition-all duration-500"
             style="width: {{ $revenueProgress }}%"></div>
    </div>
    <p class="text-xs text-gray-500 mt-2">₹{{ number_format($revenueCollected) }} of ₹{{ number_format($campaign->total_amount) }} collected</p>
</div>

@php
    $canLink = in_array(auth()->user()->role, ['owner','manager']);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Milestones (Drag & Drop + Link Invoice) --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-bold text-gray-900">Milestones ({{ $totalMilestones }})</h3>
            @if($totalMilestones > 1)
                <span class="text-xs text-gray-500">
                    <i class="fas fa-arrows-up-down-left-right mr-1"></i>Drag to reorder
                </span>
            @endif
        </div>

        <div id="msReorderMsg" class="text-xs mt-2 mb-4 hidden"></div>

        <div id="milestoneList" class="space-y-2 mb-6">
            @forelse($campaign->milestones as $ms)
                <div class="milestone-row border border-gray-200 rounded-xl p-3 hover:bg-gray-50 transition"
                     data-id="{{ $ms->id }}">

                    <div class="flex items-start gap-3" x-data="{ editing:false }">

                        {{-- Drag Handle --}}
                        <button type="button"
                                class="drag-handle mt-1 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center flex-shrink-0"
                                title="Drag to reorder">
                            <i class="fas fa-grip-vertical"></i>
                        </button>

                        {{-- View --}}
                        <div class="flex-1 min-w-0" x-show="!editing">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold {{ $ms->status === 'completed' ? 'text-gray-400 line-through' : 'text-gray-900' }} truncate">
                                        {{ $ms->title }}
                                    </p>

                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">
                                            {{ ucfirst(str_replace('_',' ',$ms->status)) }}
                                        </span>
                                        @if($ms->amount > 0)
                                            <span>₹{{ number_format($ms->amount) }}</span>
                                        @endif
                                        @if($ms->due_date)
                                            <span>Due: {{ $ms->due_date->format('M d') }}</span>
                                        @endif

                                        {{-- ✅ Linked invoice display --}}
                                        @if($ms->invoice_id)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-semibold">
                                                <i class="fas fa-link mr-1"></i>
                                                @if($ms->invoice)
                                                    <a class="hover:underline" href="{{ route('dashboard.invoices.show', $ms->invoice) }}">
                                                        {{ $ms->invoice->invoice_number }}
                                                    </a>
                                                @else
                                                    Linked Invoice
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($ms->status !== 'completed')
                                        <form method="POST" action="{{ route('dashboard.milestones.complete', $ms) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700">
                                                <i class="fas fa-check mr-1"></i>Done
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button" @click="editing=true"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    <form method="POST" action="{{ route('dashboard.milestones.destroy', $ms) }}"
                                          onsubmit="return confirm('Delete this milestone?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>

                                    
                        @if(in_array(auth()->user()->role, ['owner','manager']) && !$ms->invoice_id)
    <form method="POST" action="{{ route('dashboard.milestones.create-invoice', $ms) }}"
          onsubmit="return confirm('Create a draft invoice for this milestone?')">
        @csrf
        <button type="submit"
                class="px-2.5 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700">
            <i class="fas fa-file-invoice mr-1"></i>Invoice
        </button>
    </form>
@endif

@if($ms->invoice_id && $ms->invoice)
    <a href="{{ route('dashboard.invoices.show', $ms->invoice) }}"
       class="px-2.5 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100">
        <i class="fas fa-arrow-up-right-from-square mr-1"></i>Open
    </a>
@endif
                                </div>
                            </div>
                        </div>
                        {{-- Edit --}}
                        <div class="flex-1" x-show="editing" x-cloak>
                            <form method="POST" action="{{ route('dashboard.milestones.update', $ms) }}" class="space-y-2">
                                @csrf @method('PUT')

                                <input type="text" name="title" value="{{ $ms->title }}" required
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">

                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" name="amount" value="{{ $ms->amount }}" step="0.01"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Amount">
                                    <input type="date" name="due_date" value="{{ $ms->due_date?->format('Y-m-d') }}"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>

                                <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach(['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed'] as $k=>$v)
                                        <option value="{{ $k }}" @selected($ms->status===$k)>{{ $v }}</option>
                                    @endforeach
                                </select>

                                {{-- ✅ Invoice link dropdown (Owner/Manager only) --}}
                                @if($canLink)
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Link Invoice (optional)</label>
                                        <select name="invoice_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                            <option value="">None</option>
                                            @foreach($campaign->invoices as $inv)
                                                <option value="{{ $inv->id }}" @selected((int)$ms->invoice_id === (int)$inv->id)>
                                                    {{ $inv->invoice_number }} — ₹{{ number_format($inv->total_amount) }} ({{ ucfirst($inv->status) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">Only invoices from this campaign are shown.</p>
                                    </div>
                                @endif

                                <div class="flex gap-2 pt-1">
                                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700">
                                        Save
                                    </button>
                                    <button type="button" @click="editing=false"
                                            class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-sm text-gray-500">
                    <i class="fas fa-flag text-gray-300 text-2xl mb-2 block"></i>
                    No milestones yet.
                </div>
            @endforelse
        </div>

        {{-- Add Milestone --}}
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-bold text-gray-900 mb-3">Add Milestone</h4>
            <form method="POST" action="{{ route('dashboard.milestones.store', $campaign) }}" class="space-y-2">
                @csrf

                <input type="text" name="title" placeholder="Milestone title *" required
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">

                <div class="grid grid-cols-2 gap-2">
                    <input type="number" name="amount" placeholder="Amount (₹)" step="0.01"
                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <input type="date" name="due_date"
                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>

                {{-- ✅ Link invoice in create (Owner/Manager only) --}}
                @if($canLink && $campaign->invoices->count())
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Link Invoice (optional)</label>
                        <select name="invoice_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            <option value="">None</option>
                            @foreach($campaign->invoices as $inv)
                                <option value="{{ $inv->id }}">
                                    {{ $inv->invoice_number }} — ₹{{ number_format($inv->total_amount) }} ({{ ucfirst($inv->status) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-1"></i>Add Milestone
                </button>
            </form>
        </div>
    </div>

    {{-- Linked Invoices --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Invoices ({{ $campaign->invoices->count() }})</h3>
            <a href="{{ route('dashboard.invoices.create', ['campaign_id' => $campaign->id, 'brand_id' => $campaign->brand_id]) }}"
               class="text-xs text-indigo-600 font-medium hover:underline">
                <i class="fas fa-plus mr-1"></i>New Invoice
            </a>
        </div>

        <div class="space-y-3">
            @forelse($campaign->invoices as $inv)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <a href="{{ route('dashboard.invoices.show', $inv) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                            {{ $inv->invoice_number }}
                        </a>
                        <p class="text-xs text-gray-500">{{ $inv->issue_date->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold">₹{{ number_format($inv->total_amount) }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $inv->status === 'paid' ? 'green' : ($inv->status === 'draft' ? 'gray' : 'blue') }}-100 text-{{ $inv->status === 'paid' ? 'green' : ($inv->status === 'draft' ? 'gray' : 'blue') }}-700">
                            {{ ucfirst($inv->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-6">No invoices linked to this campaign.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const list = document.getElementById('milestoneList');
    const msg = document.getElementById('msReorderMsg');
    if (!list) return;

    const rows = list.querySelectorAll('.milestone-row');
    if (rows.length <= 1) return;

    function showMsg(text, type='info') {
        msg.classList.remove('hidden');
        msg.className = 'text-xs mt-2 mb-4 rounded-lg px-3 py-2 ' + (
            type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' :
            type === 'error' ? 'bg-red-50 text-red-800 border border-red-200' :
            'bg-gray-50 text-gray-700 border border-gray-200'
        );
        msg.textContent = text;
    }

    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    new Sortable(list, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'bg-indigo-50',
        onEnd: async function () {
            try {
                showMsg('Saving order...', 'info');

                const payload = [...list.querySelectorAll('.milestone-row')].map((el, index) => ({
                    id: el.dataset.id,
                    sort_order: index + 1
                }));

                const res = await fetch("{{ route('dashboard.milestones.reorder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ milestones: payload })
                });

                if (!res.ok) {
                    showMsg('Failed to save order. Please refresh and try again.', 'error');
                    return;
                }

                showMsg('Order saved successfully.', 'success');
                setTimeout(() => msg.classList.add('hidden'), 1500);
            } catch (e) {
                showMsg('Error saving order. Please try again.', 'error');
            }
        }
    });
})();
</script>
@endpush
@endsection