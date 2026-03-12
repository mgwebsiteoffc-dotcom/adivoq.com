<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['invoice', 'brand'])
            ->latest()
            ->paginate(20);

        return view('tenant.payments.index', compact('payments'));
    }

    public function create()
    {
        $invoices = Invoice::whereNotIn('status', ['paid', 'cancelled'])
            ->orderBy('issue_date', 'desc')
            ->get();

        return view('tenant.payments.create', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        // ✅ Create Payment
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'brand_id' => $invoice->brand_id,
            'campaign_id' => $invoice->campaign_id,
            'amount' => $request->amount,
            'currency' => $invoice->currency,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes,
            'status' => 'confirmed',
        ]);

        // ✅ Update Invoice
        $invoice->amount_paid += $request->amount;
        $invoice->balance_due = $invoice->total_amount - $invoice->amount_paid;

        if ($invoice->balance_due <= 0) {
            $invoice->status = 'paid';
            $invoice->balance_due = 0;
            $invoice->paid_at = now();
        } else {
            $invoice->status = 'partially_paid';
        }

        $invoice->save();
        
        
        // ✅ Auto Revenue Split
$teamMembers = \App\Models\TeamMember::where('tenant_id', session('tenant_id'))
    ->where('status','active')
    ->get();

foreach ($teamMembers as $member) {

    if (!$member->split_value) continue;

    $amount = 0;

    if ($member->split_type === 'percentage') {
        $amount = ($request->amount * $member->split_value) / 100;
    }

    if ($member->split_type === 'fixed') {
        $amount = $member->split_value;
    }

    \App\Models\RevenueSplit::create([
        'payment_id' => $payment->id,
        'team_member_id' => $member->id,
        'split_type' => $member->split_type,
        'split_value' => $member->split_value,
        'amount' => $amount,
        'status' => 'pending',
    ]);
}

        return redirect()->route('tenant.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['invoice', 'brand']);
        return view('tenant.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;

        // ✅ Reverse invoice amounts
        $invoice->amount_paid -= $payment->amount;
        $invoice->balance_due = $invoice->total_amount - $invoice->amount_paid;

        if ($invoice->amount_paid == 0) {
            $invoice->status = 'sent';
        } else {
            $invoice->status = 'partially_paid';
        }

        $invoice->save();

        $payment->delete();

        return redirect()->route('tenant.payments.index')
            ->with('success', 'Payment deleted.');
    }
}