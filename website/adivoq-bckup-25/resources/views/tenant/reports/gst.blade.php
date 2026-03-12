@extends('layouts.tenant')

@section('title','GST Summary')
@section('page_title','GST Summary')

@section('content')

<div class="space-y-8">

    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-white mb-6">
            GST & TDS Summary
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">

            <div>
                <p class="text-surface-400 text-sm">CGST</p>
                <p class="text-white font-semibold mt-1">
                    ₹{{ number_format($summary['cgst'],2) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">SGST</p>
                <p class="text-white font-semibold mt-1">
                    ₹{{ number_format($summary['sgst'],2) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">IGST</p>
                <p class="text-white font-semibold mt-1">
                    ₹{{ number_format($summary['igst'],2) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">Total GST Collected</p>
                <p class="text-green-400 font-semibold mt-1">
                    ₹{{ number_format($summary['tax_total'],2) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">Total TDS Deducted</p>
                <p class="text-red-400 font-semibold mt-1">
                    ₹{{ number_format($summary['tds_total'],2) }}
                </p>
            </div>

            <div>
                <p class="text-surface-400 text-sm">Invoice Total</p>
                <p class="text-white font-semibold mt-1">
                    ₹{{ number_format($summary['invoice_total'],2) }}
                </p>
            </div>

        </div>

    </div>

</div>

@endsection