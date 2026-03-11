@extends('layouts.tenant')
@section('title', 'Invoices')
@section('page_title', 'Invoice Management')

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
    @foreach([
        ['label' => 'Total', 'value' => $stats['total'], 'color' => 'gray'],
        ['label' => 'Draft', 'value' => $stats['draft'], 'color' => 'gray'],
        ['label' => 'Sent', 'value' => $stats['sent'], 'color' => 'blue'],
        ['label' => 'Paid', 'value' => $stats['paid'], 'color' => 'green'],
        ['label' => 'Overdue', 'value' => $stats['overdue'], 'color' => 'red'],
        ['label' => 'Outstanding', 'value' => '₹' . number_format($stats['total_outstanding']), 'color' => 'orange'],
    ] as $s)
        <a href="{{ route('dashboard.invoices.index', $s['label'] !== 'Total' && $s['label'] !== 'Outstanding' ? ['status' => strtolower($s['label'])] : []) }}"
           class="bg-white rounded-xl border border-gray-200 p-3 text-center hover:shadow-md transition {{ request('status') === strtolower($s['label']) ? 'ring-2 ring-indigo-500' : '' }}">
            <p class="text-lg font-bold text-{{ $s['color'] }}-600">{{ $s['value'] }}</p>
            <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
        </a>
    @endforeach
</div>

{{-- Recurring Management --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Recurring Invoices</h3>
            <p class="text-xs text-gray-500">Manage your recurring invoice templates</p>
        </div>
        <a href="{{ route('dashboard.invoices.recurring.index') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-sync mr-1"></i>Manage Recurring
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice # or brand..."
                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Brand</label>
            <select name="brand_id" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All</option>
                @foreach($brands as $b)<option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All</option>
                @foreach(['draft','sent','viewed','partially_paid','paid','overdue','cancelled'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Filter</button>
        @if(request()->hasAny(['search','brand_id','status','date_from','date_to']))
            <a href="{{ route('dashboard.invoices.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200">Clear</a>
        @endif
    </form>
</div>

{{-- Add Button --}}
<div class="flex justify-end mb-4">
    <a href="{{ route('dashboard.invoices.create') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 shadow-sm">
        <i class="fas fa-plus mr-1.5"></i>Create Invoice
    </a>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Invoice</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Brand</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Due</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Amount</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Due</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $inv)
                    @php
                        $statusColors = [
                            'draft' => 'gray', 'sent' => 'blue', 'viewed' => 'purple',
                            'partially_paid' => 'yellow', 'paid' => 'green', 'overdue' => 'red', 'cancelled' => 'gray',
                        ];
                        $color = $statusColors[$inv->status] ?? 'gray';
                        if ($inv->isOverdue() && $inv->status !== 'cancelled') $color = 'red';
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <a href="{{ route('dashboard.invoices.show', $inv) }}" class="font-semibold text-gray-900 hover:text-indigo-600 transition">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $inv->brand->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $inv->issue_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-xs {{ $inv->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                            {{ $inv->due_date->format('M d, Y') }}
                            @if($inv->isOverdue())<br><span class="text-red-500">{{ now()->diffInDays($inv->due_date) }}d overdue</span>@endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">₹{{ number_format($inv->total_amount) }}</td>
                        <td class="px-4 py-3 text-right {{ $inv->amount_due > 0 ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                            {{ $inv->amount_due > 0 ? '₹' . number_format($inv->amount_due) : '✓' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-700">
                                {{ ucfirst(str_replace('_', ' ', $inv->isOverdue() && !in_array($inv->status, ['paid','cancelled']) ? 'overdue' : $inv->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('dashboard.invoices.show', $inv) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 rounded transition" title="View"><i class="fas fa-eye text-xs"></i></a>
                                <a href="{{ route('dashboard.invoices.pdf', $inv) }}" class="p-1.5 text-gray-400 hover:text-green-600 rounded transition" title="PDF"><i class="fas fa-download text-xs"></i></a>
                                @if($inv->isDraft())
                                    <a href="{{ route('dashboard.invoices.edit', $inv) }}" class="p-1.5 text-gray-400 hover:text-blue-600 rounded transition" title="Edit"><i class="fas fa-edit text-xs"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-file-invoice text-indigo-400 text-xl"></i></div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1">No invoices yet</h3>
                            <p class="text-sm text-gray-500 mb-4">Create your first professional invoice in minutes.</p>
                            <a href="{{ route('dashboard.invoices.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700"><i class="fas fa-plus mr-1.5"></i>Create First Invoice</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())<div class="px-4 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>@endif
</div>
@endsection