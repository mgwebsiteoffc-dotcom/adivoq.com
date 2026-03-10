<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Services\InvoicePdfService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Milestone;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['brand', 'campaign']);

        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('total_amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('total_amount', '<=', $request->amount_max);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('brand', fn($q2) => $q2->where('name', 'like', "%{$s}%"));
            });
        }

        $invoices = $query->latest('issue_date')->paginate(20)->appends($request->query());
        $brands = Brand::active()->orderBy('name')->get();

        // Stats
        $stats = [
            'total' => Invoice::count(),
            'draft' => Invoice::where('status', 'draft')->count(),
            'sent' => Invoice::whereIn('status', ['sent', 'viewed'])->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::overdue()->count(),
            'total_outstanding' => Invoice::whereIn('status', ['sent', 'viewed', 'partially_paid'])->sum('amount_due'),
        ];

        return view('tenant.invoices.index', compact('invoices', 'brands', 'stats'));
    }

    public function create(Request $request)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->canCreateInvoice()) {
            return back()->with('error', 'You have reached the invoice limit for your plan this month. Please upgrade.');
        }

        $brands = Brand::active()->orderBy('name')->get();
        $campaigns = Campaign::active()->orderBy('name')->get();
        $invoiceSetting = $tenant->invoiceSetting;
        $taxSetting = $tenant->taxSetting;
        $states = config('invoicehero.indian_states');

        // Pre-select brand/campaign if passed via query
        $selectedBrandId = $request->brand_id;
        $selectedCampaignId = $request->campaign_id;

        // Generate next invoice number
        $nextNumber = $invoiceSetting
            ? $invoiceSetting->invoice_prefix . '-' . str_pad($invoiceSetting->next_invoice_number, 5, '0', STR_PAD_LEFT)
            : 'INV-00001';

        return view('tenant.invoices.form', compact(
            'brands', 'campaigns', 'invoiceSetting', 'taxSetting', 'states',
            'selectedBrandId', 'selectedCampaignId', 'nextNumber'
        ))->with('invoice', null);
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->canCreateInvoice()) {
            return back()->with('error', 'Invoice limit reached for your plan.');
        }

        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_terms' => 'required|string',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'gst_rate' => 'required|numeric|min:0|max:28',
            'tds_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:2000',
            'terms_and_conditions' => 'nullable|string|max:5000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.hsn_sac_code' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $invoiceSetting = $tenant->invoiceSetting;
            $invoiceNumber = $invoiceSetting->generateInvoiceNumber();

            $brand = Brand::findOrFail($request->brand_id);
            $gstRate = $request->gst_rate;
            $tdsRate = $request->tds_rate ?? 0;

            // Determine GST type
            $tenantStateCode = $tenant->state_code ?? $tenant->taxSetting?->state_code;
            $brandStateCode = $brand->state_code;
            $sameState = $tenantStateCode && $brandStateCode && $tenantStateCode === $brandStateCode;

            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'brand_id' => $request->brand_id,
                'campaign_id' => $request->campaign_id,
                'invoice_number' => $invoiceNumber,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'status' => 'draft',
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value ?? 0,
                'cgst_rate' => $sameState ? $gstRate / 2 : 0,
                'sgst_rate' => $sameState ? $gstRate / 2 : 0,
                'igst_rate' => !$sameState ? $gstRate : 0,
                'tds_rate' => $tdsRate,
                'currency' => $request->currency ?? 'INR',
                'payment_terms' => $request->payment_terms,
                'notes' => $request->notes,
                'terms_and_conditions' => $request->terms_and_conditions,
            ]);

            // Create line items
            foreach ($request->items as $index => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['quantity'] * $item['unit_price'],
                    'hsn_sac_code' => $item['hsn_sac_code'] ?? null,
                    'tax_rate' => $gstRate,
                    'sort_order' => $index,
                ]);
            }

            // Calculate totals
            $invoice->calculateTotals();

            // Increment monthly count
            $tenant->increment('monthly_invoice_count');

            // Log activity
            $invoice->logActivity('created', 'Invoice created');

            DB::commit();

            return redirect()->route('dashboard.invoices.show', $invoice)
                ->with('success', "Invoice {$invoiceNumber} created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['brand', 'campaign', 'items', 'payments', 'activities.user', 'milestones']);

        $tenant = auth()->user()->tenant;
        $bankDetails = $tenant->primaryBank ?? $tenant->bankDetails()->first();
        $invoiceSetting = $tenant->invoiceSetting;

        return view('tenant.invoices.show', compact('invoice', 'tenant', 'bankDetails', 'invoiceSetting'));
    }

    public function edit(Invoice $invoice)
    {
        if (!$invoice->isDraft()) {
            return back()->with('error', 'Only draft invoices can be edited.');
        }

        $invoice->load('items');
        $tenant = auth()->user()->tenant;
        $brands = Brand::active()->orderBy('name')->get();
        $campaigns = Campaign::orderBy('name')->get();
        $invoiceSetting = $tenant->invoiceSetting;
        $taxSetting = $tenant->taxSetting;
        $states = config('invoicehero.indian_states');

        return view('tenant.invoices.form', compact(
            'invoice', 'brands', 'campaigns', 'invoiceSetting', 'taxSetting', 'states'
        ))->with('selectedBrandId', $invoice->brand_id)
          ->with('selectedCampaignId', $invoice->campaign_id)
          ->with('nextNumber', $invoice->invoice_number);
    }

    public function update(Request $request, Invoice $invoice)
    {
        if (!$invoice->isDraft()) {
            return back()->with('error', 'Only draft invoices can be edited.');
        }

        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_terms' => 'required|string',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'gst_rate' => 'required|numeric|min:0|max:28',
            'tds_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:2000',
            'terms_and_conditions' => 'nullable|string|max:5000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.hsn_sac_code' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $tenant = auth()->user()->tenant;
            $brand = Brand::findOrFail($request->brand_id);
            $gstRate = $request->gst_rate;
            $tdsRate = $request->tds_rate ?? 0;

            $tenantStateCode = $tenant->state_code ?? $tenant->taxSetting?->state_code;
            $brandStateCode = $brand->state_code;
            $sameState = $tenantStateCode && $brandStateCode && $tenantStateCode === $brandStateCode;

            $invoice->update([
                'brand_id' => $request->brand_id,
                'campaign_id' => $request->campaign_id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value ?? 0,
                'cgst_rate' => $sameState ? $gstRate / 2 : 0,
                'sgst_rate' => $sameState ? $gstRate / 2 : 0,
                'igst_rate' => !$sameState ? $gstRate : 0,
                'tds_rate' => $tdsRate,
                'payment_terms' => $request->payment_terms,
                'notes' => $request->notes,
                'terms_and_conditions' => $request->terms_and_conditions,
            ]);

            // Replace items
            $invoice->items()->delete();
            foreach ($request->items as $index => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['quantity'] * $item['unit_price'],
                    'hsn_sac_code' => $item['hsn_sac_code'] ?? null,
                    'tax_rate' => $gstRate,
                    'sort_order' => $index,
                ]);
            }

            $invoice->calculateTotals();
            $invoice->logActivity('updated', 'Invoice updated');

            DB::commit();

            return redirect()->route('dashboard.invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    public function sendEmail(Invoice $invoice)
    {
        if ($invoice->isDraft()) {
            $invoice->update(['status' => 'sent', 'sent_at' => now()]);
        }

        $brand = $invoice->brand;
        if (!$brand->email) {
            return back()->with('error', 'Brand has no email address. Please update brand details.');
        }

        try {
            $pdfService = new InvoicePdfService();
            $pdf = $pdfService->generate($invoice);
            $tenant = auth()->user()->tenant;

            Mail::raw(
                "Dear {$brand->contact_person},\n\n" .
                "Please find attached invoice {$invoice->invoice_number} for ₹" . number_format($invoice->total_amount, 2) . ".\n\n" .
                "Due Date: {$invoice->due_date->format('d M Y')}\n\n" .
                ($invoice->payment_link_token
                    ? "Pay online: " . route('payment.link', $invoice->payment_link_token) . "\n\n"
                    : '') .
                "Thank you,\n{$tenant->business_name}",
                function ($message) use ($brand, $invoice, $pdf, $tenant) {
                    $message->to($brand->email, $brand->contact_person ?? $brand->name)
                        ->subject("Invoice {$invoice->invoice_number} from {$tenant->business_name}")
                        ->attachData($pdf->output(), "Invoice-{$invoice->invoice_number}.pdf", [
                            'mime' => 'application/pdf',
                        ]);
                }
            );

            if ($invoice->status === 'draft') {
                $invoice->update(['status' => 'sent', 'sent_at' => now()]);
            }

            $invoice->logActivity('sent_email', "Invoice sent via email to {$brand->email}");

            return back()->with('success', "Invoice sent to {$brand->email}!");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function sendWhatsApp(Invoice $invoice)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant->hasFeature('whatsapp_sending')) {
            return back()->with('error', 'WhatsApp sending is not available on your plan. Please upgrade.');
        }

        $brand = $invoice->brand;
        if (!$brand->phone) {
            return back()->with('error', 'Brand has no phone number.');
        }

        // Generate payment link if not exists
        if (!$invoice->payment_link_token) {
            $invoice->generatePaymentLink();
        }

        if ($invoice->isDraft()) {
            $invoice->update(['status' => 'sent', 'sent_at' => now()]);
        }

        $whatsapp = new WhatsAppService();
        $result = $whatsapp->sendInvoiceNotification($invoice, $brand->phone);

        if ($result['success']) {
            $invoice->logActivity('sent_whatsapp', "Invoice sent via WhatsApp to {$brand->phone}");
            return back()->with('success', "Invoice sent via WhatsApp to {$brand->phone}!");
        }

        return back()->with('error', 'WhatsApp sending failed: ' . ($result['error'] ?? 'Unknown error'));
    }

    public function generatePaymentLink(Invoice $invoice)
    {
        $link = $invoice->generatePaymentLink();

        $invoice->logActivity('payment_link_generated', 'Payment link generated');

        return back()->with('success', 'Payment link generated!')
            ->with('payment_link', $link);
    }

    public function downloadPdf(Invoice $invoice)
    {
        $pdfService = new InvoicePdfService();

        $invoice->logActivity('pdf_downloaded', 'PDF downloaded');

        return $pdfService->download($invoice);
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->amount_due,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:bank_transfer,upi,cash,cheque,razorpay,stripe,other',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $receiptPath = $request->file('receipt')->store('receipts', 'public');
            }

            $payment = Payment::create([
                'tenant_id' => auth()->user()->tenant_id,
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference,
                'receipt_path' => $receiptPath,
                'notes' => $request->notes,
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Update invoice
            $invoice->recordPayment($request->amount);

            $invoice->logActivity('payment_recorded', "Payment of ₹" . number_format($request->amount, 2) . " recorded", [
                'payment_id' => $payment->id,
                'method' => $request->payment_method,
            ]);

            // Send WhatsApp confirmation if enabled
            $notifSettings = auth()->user()->tenant->notificationSetting;
            if ($notifSettings?->whatsapp_on_payment_received && $invoice->brand->phone) {
                $whatsapp = new WhatsAppService();
                $whatsapp->sendPaymentConfirmation($payment, $invoice->brand->phone);
            }

            DB::commit();

            return back()->with('success', "Payment of ₹" . number_format($request->amount, 2) . " recorded successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    public function sendReminder(Invoice $invoice)
    {
        $brand = $invoice->brand;

        // Email reminder
        if ($brand->email) {
            try {
                $tenant = auth()->user()->tenant;
                Mail::raw(
                    "Dear {$brand->contact_person},\n\n" .
                    "This is a friendly reminder that invoice {$invoice->invoice_number} " .
                    "for ₹" . number_format($invoice->amount_due, 2) . " is " .
                    ($invoice->isOverdue() ? "overdue" : "due on {$invoice->due_date->format('d M Y')}") . ".\n\n" .
                    ($invoice->payment_link_token
                        ? "Pay online: " . route('payment.link', $invoice->payment_link_token) . "\n\n"
                        : '') .
                    "Please process the payment at the earliest.\n\nThank you,\n{$tenant->business_name}",
                    function ($message) use ($brand, $invoice, $tenant) {
                        $message->to($brand->email)
                            ->subject("Payment Reminder: Invoice {$invoice->invoice_number}");
                    }
                );
            } catch (\Exception $e) {
                // silently fail email
            }
        }

        // WhatsApp reminder
        $notifSettings = auth()->user()->tenant->notificationSetting;
        if ($notifSettings?->whatsapp_on_invoice_overdue && $brand->phone) {
            $whatsapp = new WhatsAppService();
            $whatsapp->sendPaymentReminder($invoice, $brand->phone);
        }

        $invoice->logActivity('reminder_sent', 'Payment reminder sent');

        return back()->with('success', 'Payment reminder sent!');
    }

    public function duplicate(Invoice $invoice)
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant->canCreateInvoice()) {
            return back()->with('error', 'Invoice limit reached.');
        }

        try {
            DB::beginTransaction();

            $invoiceSetting = $tenant->invoiceSetting;
            $newNumber = $invoiceSetting->generateInvoiceNumber();

            $newInvoice = $invoice->replicate();
            $newInvoice->invoice_number = $newNumber;
            $newInvoice->status = 'draft';
            $newInvoice->issue_date = now()->toDateString();
            $newInvoice->due_date = now()->addDays($invoiceSetting->default_payment_terms_days ?? 30)->toDateString();
            $newInvoice->amount_paid = 0;
            $newInvoice->amount_due = $invoice->net_receivable;
            $newInvoice->payment_link_token = null;
            $newInvoice->payment_link_expires_at = null;
            $newInvoice->sent_at = null;
            $newInvoice->viewed_at = null;
            $newInvoice->paid_at = null;
            $newInvoice->cancelled_at = null;
            $newInvoice->save();

            // Duplicate items
            foreach ($invoice->items as $item) {
                $newItem = $item->replicate();
                $newItem->invoice_id = $newInvoice->id;
                $newItem->save();
            }

            $tenant->increment('monthly_invoice_count');
            $newInvoice->logActivity('duplicated', "Duplicated from {$invoice->invoice_number}");

            DB::commit();

            return redirect()->route('dashboard.invoices.edit', $newInvoice)
                ->with('success', "Invoice duplicated as {$newNumber}. You can edit before sending.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to duplicate: ' . $e->getMessage());
        }
    }

    public function cancel(Invoice $invoice)
    {
        if ($invoice->isPaid()) {
            return back()->with('error', 'Cannot cancel a fully paid invoice.');
        }

        $invoice->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $invoice->logActivity('cancelled', 'Invoice cancelled');

        return back()->with('success', 'Invoice cancelled.');
    }

    public function createFromMilestone(Milestone $milestone)
{
    // Owner/Manager only (linking milestone→invoice)
    if (!in_array(auth()->user()->role, ['owner', 'manager'])) {
        abort(403, 'Only owner/manager can create invoices from milestones.');
    }

    $tenant = auth()->user()->tenant;

    if (!$tenant->canCreateInvoice()) {
        return back()->with('error', 'Invoice limit reached for your plan.');
    }

    $milestone->load(['campaign.brand']);

    $campaign = $milestone->campaign;
    $brand = $campaign?->brand;

    if (!$campaign || !$brand) {
        return back()->with('error', 'Milestone must belong to a campaign with a brand.');
    }

    try {
        DB::beginTransaction();

        $invoiceSetting = $tenant->invoiceSetting;
        $taxSetting = $tenant->taxSetting;

        $invoiceNumber = $invoiceSetting ? $invoiceSetting->generateInvoiceNumber() : ('INV-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT));

        $issueDate = now()->toDateString();
        $dueDays = $invoiceSetting?->default_payment_terms_days ?? 30;
        $dueDate = now()->addDays($dueDays)->toDateString();

        // Defaults
        $gstRate = (float) ($taxSetting?->default_igst_rate ?? 18);
        $tdsRate = (float) ($taxSetting?->default_tds_rate ?? 0);

        // Determine GST type (same state => CGST+SGST else IGST)
        $tenantStateCode = $tenant->state_code ?? $taxSetting?->state_code;
        $brandStateCode = $brand->state_code;
        $sameState = $tenantStateCode && $brandStateCode && $tenantStateCode === $brandStateCode;

        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'brand_id' => $brand->id,
            'campaign_id' => $campaign->id,
            'invoice_number' => $invoiceNumber,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'status' => 'draft',

            'discount_type' => null,
            'discount_value' => 0,

            'cgst_rate' => $sameState ? $gstRate / 2 : 0,
            'sgst_rate' => $sameState ? $gstRate / 2 : 0,
            'igst_rate' => !$sameState ? $gstRate : 0,

            'tds_rate' => $tdsRate,
            'currency' => 'INR',

            'payment_terms' => $invoiceSetting?->default_payment_terms ?? 'net_30',
            'payment_terms_days' => $dueDays,

            'notes' => $invoiceSetting?->default_notes,
            'terms_and_conditions' => $invoiceSetting?->default_terms_and_conditions,
        ]);

       // One line item from milestone
$amount = (float) ($milestone->amount ?? 0);

InvoiceItem::create([
    'invoice_id' => $invoice->id,
    'description' => "Milestone: {$milestone->title}" . ($campaign ? " ({$campaign->name})" : ''),
    'quantity' => 1,
    'unit_price' => $amount,   // can be 0
    'amount' => $amount,
    'hsn_sac_code' => null,
    'tax_rate' => $gstRate,
    'sort_order' => 1,
]);

$invoice->calculateTotals();

// ✅ Link milestone -> invoice
$milestone->update(['invoice_id' => $invoice->id]);

$tenant->increment('monthly_invoice_count');

$invoice->logActivity('created_from_milestone', "Created from milestone: {$milestone->title}", [
    'milestone_id' => $milestone->id,
    'campaign_id' => $campaign->id,
    'milestone_amount' => $amount,
]);

DB::commit();

// ✅ warning for 0 amount
$warning = null;
if ($amount <= 0) {
    $warning = "This milestone amount is ₹0. Please update the invoice amount before sending.";
}

return redirect()->route('dashboard.invoices.edit', $invoice)
    ->with('success', "Invoice {$invoiceNumber} created from milestone. Please review and send.")
    ->when($warning, fn($r) => $r->with('warning', $warning));

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to create invoice: ' . $e->getMessage());
    }
}
}