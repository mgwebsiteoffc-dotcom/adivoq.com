@extends('layouts.tenant')
@section('title','GST Returns')
@section('page_title','GST Returns (CA Export)')

@section('content')
<div class="bg-white rounded-xl border p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from', $from->toDateString()) }}" class="px-3 py-2 border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to', $to->toDateString()) }}" class="px-3 py-2 border rounded-lg text-sm">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Apply</button>

        @if(request()->query())
            <a href="{{ route('dashboard.tax.returns') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-200">Reset</a>
        @endif
    </form>
</div>

<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">Invoices</p><p class="text-xl font-black">{{ $summary['count'] }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">Taxable</p><p class="text-xl font-black">₹{{ number_format($summary['taxable'],2) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">CGST</p><p class="text-xl font-black">₹{{ number_format($summary['cgst'],2) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">SGST</p><p class="text-xl font-black">₹{{ number_format($summary['sgst'],2) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">IGST</p><p class="text-xl font-black">₹{{ number_format($summary['igst'],2) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500 font-bold">GST Total</p><p class="text-xl font-black text-green-700">₹{{ number_format($summary['gst_total'],2) }}</p></div>
</div>

<div class="bg-white rounded-xl border p-5 mb-6">
    <h3 class="text-sm font-black text-gray-900 mb-3">CA Exports (GSTR‑1 friendly)</h3>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('dashboard.tax.returns.export', ['type'=>'gstr1_b2b_detailed'] + request()->query()) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-black hover:bg-green-700">
            <i class="fas fa-download mr-2"></i>B2B Detailed (Rate-wise)
        </a>

        <a href="{{ route('dashboard.tax.returns.export', ['type'=>'gstr1_b2cl'] + request()->query()) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-black hover:bg-green-700">
            <i class="fas fa-download mr-2"></i>B2CL (≥ ₹{{ number_format($b2clThreshold) }})
        </a>

        <a href="{{ route('dashboard.tax.returns.export', ['type'=>'gstr1_b2cs'] + request()->query()) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-black hover:bg-green-700">
            <i class="fas fa-download mr-2"></i>B2CS Summary
        </a>

        <a href="{{ route('dashboard.tax.returns.export', ['type'=>'gstr1_nil_exempt'] + request()->query()) }}"
           class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-black hover:bg-black">
            <i class="fas fa-download mr-2"></i>Nil/Exempt
        </a>

        <a href="{{ route('dashboard.tax.returns.export', ['type'=>'gstr1_hsn'] + request()->query()) }}"
           class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-black hover:bg-black">
            <i class="fas fa-download mr-2"></i>HSN Summary
        </a>

        <a href="{{ route('dashboard.tax.returns.export', ['type'=>'gstr1_cdnr_template'] + request()->query()) }}"
           class="px-4 py-2 bg-gray-100 text-gray-900 rounded-lg text-sm font-black hover:bg-gray-200 border">
            <i class="fas fa-file-lines mr-2"></i>CDNR Template
        </a>
    </div>

    <p class="text-xs text-gray-500 mt-3">
        CDNR requires Credit/Debit Notes module (not yet). Template helps your CA maintain format.
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- B2B preview --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h3 class="text-sm font-black text-gray-900">B2B (GSTIN available)</h3>
            <p class="text-xs text-gray-500 mt-1">Invoices: {{ $b2b->count() }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">GSTIN</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Invoice</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Taxable</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">GST</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($b2b->take(8) as $i)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $i->brand->gstin }}</td>
                            <td class="px-4 py-3">
                                <a class="font-bold text-indigo-600 hover:underline" href="{{ route('dashboard.invoices.show', $i) }}">{{ $i->invoice_number }}</a>
                                <div class="text-xs text-gray-500">{{ $i->issue_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-bold">₹{{ number_format($i->taxable_amount,2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-green-700">₹{{ number_format($i->total_tax,2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">No B2B invoices.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- B2CS summary preview --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h3 class="text-sm font-black text-gray-900">B2CS Summary (POS + Rate)</h3>
            <p class="text-xs text-gray-500 mt-1">Groups: {{ $b2csSummary->count() }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">POS</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Rate</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Taxable</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">GST</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($b2csSummary->take(10) as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $r['pos_state_code'] ?: 'NA' }}</td>
                            <td class="px-4 py-3 text-right font-bold">{{ $r['rate'] }}%</td>
                            <td class="px-4 py-3 text-right font-bold">₹{{ number_format($r['taxable'],2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-green-700">₹{{ number_format($r['gst_total'],2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">No B2CS data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-6 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl p-4 text-sm">
    <strong>Note:</strong> This is CA-friendly export. Please verify POS/state codes and exemptions with your CA before filing.
</div>
@endsection