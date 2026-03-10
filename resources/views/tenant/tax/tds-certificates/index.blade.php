@extends('layouts.tenant')
@section('title','TDS Certificates')
@section('page_title','TDS Certificates')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">Total</p><p class="text-2xl font-black">{{ $stats['total'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">Pending</p><p class="text-2xl font-black text-orange-600">{{ $stats['pending'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">Verified</p><p class="text-2xl font-black text-green-600">{{ $stats['verified'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">TDS Total</p><p class="text-2xl font-black text-red-600">₹{{ number_format($stats['tds_total'],2) }}</p></div>
</div>

<div class="flex flex-wrap items-end justify-between gap-3 mb-4">
    <form method="GET" class="bg-white rounded-xl border p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">FY</label>
            <input name="financial_year" value="{{ request('financial_year') }}" placeholder="2024-25" class="px-3 py-2 border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Quarter</label>
            <select name="quarter" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">All</option>
                @foreach(['Q1','Q2','Q3','Q4'] as $q)
                    <option value="{{ $q }}" @selected(request('quarter')===$q)>{{ $q }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border rounded-lg text-sm">
                <option value="">All</option>
                <option value="pending" @selected(request('status')==='pending')>Pending</option>
                <option value="verified" @selected(request('status')==='verified')>Verified</option>
            </select>
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">Filter</button>
        @if(request()->query())
            <a href="{{ route('dashboard.tds-certificates.index') }}" class="px-4 py-2 bg-gray-100 rounded-lg text-sm font-black">Reset</a>
        @endif
    </form>

    <a href="{{ route('dashboard.tds-certificates.create') }}"
       class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">
        <i class="fas fa-plus mr-2"></i>Add Certificate
    </a>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-bold text-gray-600">FY/Q</th>
                    <th class="text-left px-4 py-3 font-bold text-gray-600">Brand</th>
                    <th class="text-left px-4 py-3 font-bold text-gray-600">Invoice</th>
                    <th class="text-left px-4 py-3 font-bold text-gray-600">Cert #</th>
                    <th class="text-right px-4 py-3 font-bold text-gray-600">TDS</th>
                    <th class="text-left px-4 py-3 font-bold text-gray-600">Status</th>
                    <th class="text-right px-4 py-3 font-bold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($certs as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $c->financial_year ?? '—' }} {{ $c->quarter ? '• '.$c->quarter : '' }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $c->brand->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($c->invoice)
                                <a class="font-black text-indigo-600 hover:underline" href="{{ route('dashboard.invoices.show',$c->invoice) }}">{{ $c->invoice->invoice_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $c->certificate_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-black text-red-600">₹{{ number_format($c->tds_amount,2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs font-black rounded-full {{ $c->status==='verified'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($c->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($c->file_path)
                                <a class="text-xs font-black text-gray-700 hover:underline mr-2" target="_blank" href="{{ asset('storage/'.$c->file_path) }}">File</a>
                            @endif
                            <a class="text-xs font-black text-blue-600 hover:underline mr-2" href="{{ route('dashboard.tds-certificates.edit',$c) }}">Edit</a>
                            <form method="POST" action="{{ route('dashboard.tds-certificates.destroy',$c) }}" class="inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-black text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-14 text-center text-gray-500">No certificates.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t">{{ $certs->links() }}</div>
</div>
@endsection