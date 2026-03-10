<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TaxSetting;
use App\Models\Invoice;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $tax = $tenant->taxSetting;

        $year = (int) ($request->year ?? now()->year);

        $invoices = Invoice::whereYear('issue_date', $year)->get();

        $summary = [
            'cgst' => $invoices->sum('cgst_amount'),
            'sgst' => $invoices->sum('sgst_amount'),
            'igst' => $invoices->sum('igst_amount'),
            'gst_total' => $invoices->sum('total_tax'),
            'tds_total' => Invoice::whereYear('issue_date', $year)->whereIn('status', ['paid','partially_paid'])->sum('tds_amount'),
        ];

        $states = config('invoicehero.indian_states');

        return view('tenant.tax.index', compact('tenant','tax','summary','states','year'));
    }

    public function update(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $data = $request->validate([
            'pan_number' => 'nullable|string|max:10',
            'gstin' => 'nullable|string|max:15',
            'gst_registered' => 'nullable|boolean',
            'state_code' => 'nullable|string|max:5',
            'default_cgst_rate' => 'required|numeric|min:0|max:28',
            'default_sgst_rate' => 'required|numeric|min:0|max:28',
            'default_igst_rate' => 'required|numeric|min:0|max:28',
            'default_tds_rate' => 'required|numeric|min:0|max:100',
        ]);

        $data['gst_registered'] = $request->boolean('gst_registered');

        $tax = TaxSetting::updateOrCreate(
            ['tenant_id' => $tenant->id],
            $data
        );

        // keep tenant mirror fields also updated
        $tenant->update([
            'pan_number' => $data['pan_number'] ?? $tenant->pan_number,
            'gstin' => $data['gstin'] ?? $tenant->gstin,
            'gst_registered' => $data['gst_registered'],
            'state_code' => $data['state_code'] ?? $tenant->state_code,
        ]);

        return redirect()->route('dashboard.tax.index')->with('success', 'Tax settings updated.');
    }
}