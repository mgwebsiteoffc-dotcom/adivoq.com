<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Brand;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Milestone;


class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('brand')
            ->latest()
            ->paginate(20);

        return view('tenant.invoices.index', compact('invoices'));
    }

public function create()
{
    $brands = \App\Models\Brand::orderBy('name')->get();

    return view('tenant.invoices.create', compact('brands'));
}

public function store(Request $request)
{
    $request->validate([
        'campaign_id' => 'required|exists:campaigns,id',
        'milestone_ids' => 'required|array|min:1',
    ]);

    $campaign = Campaign::with('brand')->findOrFail($request->campaign_id);
    $milestones = Milestone::whereIn('id', $request->milestone_ids)
                            ->whereNull('invoice_id')
                            ->get();

    if ($milestones->isEmpty()) {
        return back()->with('error', 'No valid milestones selected.');
    }

    $taxSettings = \App\Models\TaxSetting::first();

    // ✅ Subtotal from milestones
    $subtotal = $milestones->sum('amount');
    $taxableAmount = $subtotal;

    // ✅ GST
    $cgst = 0;
    $sgst = 0;
    $igst = 0;
    $taxAmount = 0;

    if ($taxSettings && $taxSettings->gst_registered) {

        $tenantState = $taxSettings->state_code;
        $brandState = $campaign->brand->address['state_code'] ?? null;

        $gstRate = $taxSettings->gst_rate;

        if ($tenantState == $brandState) {
            $cgst = ($taxableAmount * ($gstRate / 2)) / 100;
            $sgst = ($taxableAmount * ($gstRate / 2)) / 100;
        } else {
            $igst = ($taxableAmount * $gstRate) / 100;
        }

        $taxAmount = $cgst + $sgst + $igst;
    }

    // ✅ TDS
    $tdsRate = $taxSettings->tds_default_rate ?? 0;
    $tdsAmount = ($taxableAmount * $tdsRate) / 100;

    $totalAmount = $taxableAmount + $taxAmount;
    $balanceDue = $totalAmount - $tdsAmount;

    $invoice = Invoice::create([
        'brand_id' => $campaign->brand_id,
        'campaign_id' => $campaign->id,
        'invoice_number' => 'INV-' . now()->format('Y') . '-' . rand(1000, 9999),
        'public_token' => \Str::random(32),
        'issue_date' => now(),
        'due_date' => now()->addDays(15),
        'subtotal' => $subtotal,
        'taxable_amount' => $taxableAmount,
        'cgst_amount' => $cgst,
        'sgst_amount' => $sgst,
        'igst_amount' => $igst,
        'tax_amount' => $taxAmount,
        'tds_rate' => $tdsRate,
        'tds_amount' => $tdsAmount,
        'total_amount' => $totalAmount,
        'amount_paid' => 0,
        'balance_due' => $balanceDue,
        'currency' => 'INR',
        'status' => 'sent',
    ]);

    // ✅ Create invoice items from milestones
    foreach ($milestones as $milestone) {

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $milestone->title,
            'quantity' => 1,
            'unit_price' => $milestone->amount,
            'amount' => $milestone->amount,
        ]);

        $milestone->update([
            'invoice_id' => $invoice->id,
        ]);
    }

    return redirect()->route('tenant.invoices.index')
        ->with('success', 'Invoice generated from milestones.');
}

    public function show(Invoice $invoice)
    {
        $invoice->load(['brand', 'items']);

        return view('tenant.invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('tenant.invoices.index')
            ->with('success', 'Invoice deleted.');
    }
}