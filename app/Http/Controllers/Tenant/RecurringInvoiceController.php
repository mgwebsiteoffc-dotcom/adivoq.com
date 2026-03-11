<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RecurringInvoiceController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->hasFeature('recurring_invoices')) {
            return back()->with('error', 'Recurring invoices are not available on your plan. Please upgrade.');
        }

        $recurringInvoices = Invoice::where('tenant_id', $tenant->id)
            ->where('is_recurring', true)
            ->with(['brand', 'campaign'])
            ->latest('created_at')
            ->paginate(20);

        return view('tenant.invoices.recurring-index', compact('recurringInvoices'));
    }
    public function edit(Invoice $invoice)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->hasFeature('recurring_invoices')) {
            return back()->with('error', 'Recurring invoices are not available on your plan. Please upgrade.');
        }

        $frequencies = ['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly'];

        return view('tenant.invoices.recurring', compact('invoice', 'frequencies'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->hasFeature('recurring_invoices')) {
            return back()->with('error', 'Recurring invoices are not available on your plan.');
        }

        // We allow recurring even if invoice is sent/paid, because it acts as a "template".
        $data = $request->validate([
            'recurring_frequency' => 'required|in:monthly,quarterly,yearly',
            'next_recurring_date' => 'required|date|after_or_equal:today',
        ]);

        $invoice->update([
            'is_recurring' => true,
            'recurring_frequency' => $data['recurring_frequency'],
            'next_recurring_date' => Carbon::parse($data['next_recurring_date'])->toDateString(),
        ]);

        $invoice->logActivity('recurring_enabled', 'Recurring enabled', $data);

        return redirect()->route('dashboard.invoices.show', $invoice)
            ->with('success', 'Recurring invoice enabled successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->update([
            'is_recurring' => false,
            'recurring_frequency' => null,
            'next_recurring_date' => null,
        ]);

        $invoice->logActivity('recurring_disabled', 'Recurring disabled');

        return redirect()->route('dashboard.invoices.show', $invoice)
            ->with('success', 'Recurring invoice disabled.');
    }

    public function pause(Invoice $invoice)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->hasFeature('recurring_invoices')) {
            return back()->with('error', 'Recurring invoices are not available on your plan. Please upgrade.');
        }

        if (!$invoice->is_recurring) {
            return back()->with('error', 'This invoice is not recurring.');
        }

        $invoice->update(['paused' => true]);

        $invoice->logActivity('recurring_paused', 'Recurring invoice paused');

        return back()->with('success', 'Recurring invoice paused successfully.');
    }

    public function resume(Invoice $invoice)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->hasFeature('recurring_invoices')) {
            return back()->with('error', 'Recurring invoices are not available on your plan. Please upgrade.');
        }

        if (!$invoice->is_recurring) {
            return back()->with('error', 'This invoice is not recurring.');
        }

        $invoice->update(['paused' => false]);

        $invoice->logActivity('recurring_resumed', 'Recurring invoice resumed');

        return back()->with('success', 'Recurring invoice resumed successfully.');
    }
}