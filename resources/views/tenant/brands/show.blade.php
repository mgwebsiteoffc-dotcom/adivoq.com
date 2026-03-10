@extends('layouts.tenant')
@section('title', $brand->name)
@section('page_title', $brand->name)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('dashboard.brands.index') }}" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left mr-1"></i>All Brands</a>
    <div class="flex items-center space-x-2">
        <a href="{{ route('dashboard.invoices.create', ['brand_id' => $brand->id]) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700"><i class="fas fa-file-invoice mr-1"></i>New Invoice</a>
        <a href="{{ route('dashboard.brands.edit', $brand) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700"><i class="fas fa-edit mr-1"></i>Edit</a>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-2xl font-bold text-green-600">₹{{ number_format($totalRevenue) }}</p>
        <p class="text-xs text-gray-500">Total Revenue</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-2xl font-bold text-orange-600">₹{{ number_format($outstanding) }}</p>
        <p class="text-xs text-gray-500">Outstanding</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-2xl font-bold text-gray-900">{{ $brand->campaigns_count }}</p>
        <p class="text-xs text-gray-500">Campaigns</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
        <p class="text-2xl font-bold text-gray-900">{{ $brand->invoices_count }}</p>
        <p class="text-xs text-gray-500">Invoices</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Brand Details --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Brand Details</h3>
        <dl class="space-y-2 text-sm">
            @foreach([
                'Contact' => $brand->contact_person,
                'Email' => $brand->email,
                'Phone' => $brand->phone,
                'Website' => $brand->website,
                'GSTIN' => $brand->gstin,
                'PAN' => $brand->pan_number,
                'State' => ($brand->state_code ? config("invoicehero.indian_states.{$brand->state_code}", '') . " ({$brand->state_code})" : null),
                'Address' => $brand->full_address ?: null,
            ] as $label => $val)
                @if($val)
                    <div class="flex justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-500">{{ $label }}</span>
                        <span class="font-medium text-gray-900 text-right max-w-[60%]">{{ $val }}</span>
                    </div>
                @endif
            @endforeach
        </dl>
    </div>

    {{-- Recent Invoices --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Invoices</h3>
        <div class="space-y-2">
            @forelse($invoices as $inv)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <a href="{{ route('dashboard.invoices.show', $inv) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $inv->invoice_number }}</a>
                    <div class="text-right">
                        <p class="text-sm font-semibold">₹{{ number_format($inv->total_amount) }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $inv->status === 'paid' ? 'green' : ($inv->status === 'overdue' ? 'red' : 'blue') }}-100 text-{{ $inv->status === 'paid' ? 'green' : ($inv->status === 'overdue' ? 'red' : 'blue') }}-700">
                            {{ ucfirst($inv->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No invoices yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Payments</h3>
        <div class="space-y-2">
            @forelse($payments as $pay)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900">₹{{ number_format($pay->amount) }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $pay->payment_method)) }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $pay->payment_date->format('M d') }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No payments yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection