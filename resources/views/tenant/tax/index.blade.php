@extends('layouts.tenant')
@section('title','Tax')
@section('page_title','Tax Management')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">CGST</p>
        <p class="text-xl font-black text-gray-900 mt-1">₹{{ number_format($summary['cgst'],2) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">SGST</p>
        <p class="text-xl font-black text-gray-900 mt-1">₹{{ number_format($summary['sgst'],2) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">IGST</p>
        <p class="text-xl font-black text-gray-900 mt-1">₹{{ number_format($summary['igst'],2) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-500 font-semibold uppercase">TDS (Paid/Partial)</p>
        <p class="text-xl font-black text-red-700 mt-1">₹{{ number_format($summary['tds_total'],2) }}</p>
    </div>
</div>

<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <a href="{{ route('dashboard.tax.returns') }}"
       class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700">
        <i class="fas fa-file-export mr-2"></i>GST Returns (GSTR Export)
    </a>

    <a href="{{ route('dashboard.tds-certificates.index') }}"
       class="px-5 py-2.5 bg-gray-100 text-gray-800 text-sm font-bold rounded-lg hover:bg-gray-200">
        <i class="fas fa-certificate mr-2"></i>TDS Certificates
    </a>
</div>

<div class="bg-white rounded-xl border p-6">
    <h3 class="text-sm font-black text-gray-900 mb-4">Tax Settings</h3>

    <form method="POST" action="{{ route('dashboard.tax.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">PAN</label>
            <input name="pan_number" value="{{ old('pan_number',$tax?->pan_number ?? $tenant->pan_number) }}" class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono uppercase">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">GSTIN</label>
            <input name="gstin" value="{{ old('gstin',$tax?->gstin ?? $tenant->gstin) }}" class="w-full px-3 py-2.5 border rounded-lg text-sm font-mono uppercase">
        </div>

        <div class="sm:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="gst_registered" value="1" class="rounded"
                   @checked(old('gst_registered',$tax?->gst_registered ?? $tenant->gst_registered))>
            <span class="text-sm font-bold text-gray-700">GST Registered</span>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">State Code</label>
            <select name="state_code" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                <option value="">Select</option>
                @foreach($states as $code => $name)
                    <option value="{{ $code }}" @selected(old('state_code',$tax?->state_code ?? $tenant->state_code)==$code)>{{ $code }} — {{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div></div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Default CGST %</label>
            <input type="number" step="0.01" name="default_cgst_rate" value="{{ old('default_cgst_rate',$tax?->default_cgst_rate ?? 9) }}"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Default SGST %</label>
            <input type="number" step="0.01" name="default_sgst_rate" value="{{ old('default_sgst_rate',$tax?->default_sgst_rate ?? 9) }}"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Default IGST %</label>
            <input type="number" step="0.01" name="default_igst_rate" value="{{ old('default_igst_rate',$tax?->default_igst_rate ?? 18) }}"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Default TDS %</label>
            <input type="number" step="0.01" name="default_tds_rate" value="{{ old('default_tds_rate',$tax?->default_tds_rate ?? 10) }}"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm">
        </div>

        <div class="sm:col-span-2 pt-3 border-t flex justify-end">
            <button class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection