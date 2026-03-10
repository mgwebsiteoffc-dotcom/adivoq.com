<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\InvoiceSetting;
use App\Models\BankDetail;
use App\Models\NotificationSetting;
use App\Models\PaymentGatewaySetting;
use App\Models\Brand;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class SettingController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;

        $invoiceSetting = $tenant->invoiceSetting;
        $bank = $tenant->primaryBank ?? $tenant->bankDetails()->first();
        $notifications = $tenant->notificationSetting;
        $gateway = $tenant->paymentGatewaySetting;
        $razorpayWebhookLogs = \App\Models\RazorpayWebhookLog::where('tenant_id', $tenant->id)
    ->latest()
    ->take(5)
    ->get();

return view('tenant.settings.index', compact('tenant','invoiceSetting','bank','notifications','gateway','razorpayWebhookLogs'));

        // return view('tenant.settings.index', compact('tenant','invoiceSetting','bank','notifications','gateway'));
    }

    public function updateProfile(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'business_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:1024',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
        ]);

        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? $user->phone,
        ]);

        if ($request->hasFile('logo')) {
            if ($tenant->logo) \Storage::disk('public')->delete($tenant->logo);
            $data['logo'] = $request->file('logo')->store('tenant-logos', 'public');
        } else {
            unset($data['logo']);
        }

        $tenant->update([
            'business_name' => $data['business_name'] ?? $tenant->business_name,
            'logo' => $data['logo'] ?? $tenant->logo,
            'address_line1' => $data['address_line1'] ?? $tenant->address_line1,
            'address_line2' => $data['address_line2'] ?? $tenant->address_line2,
            'city' => $data['city'] ?? $tenant->city,
            'state' => $data['state'] ?? $tenant->state,
            'pincode' => $data['pincode'] ?? $tenant->pincode,
        ]);

        return back()->with('success', 'Profile updated.');
    }

    public function updateInvoiceSettings(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $data = $request->validate([
            'invoice_prefix' => 'required|string|max:20',
            'default_payment_terms' => 'required|string|max:20',
            'default_payment_terms_days' => 'required|integer|min:0|max:365',
            'default_notes' => 'nullable|string|max:5000',
            'default_terms_and_conditions' => 'nullable|string|max:5000',
            'invoice_color' => 'required|string|max:7',
            'show_logo' => 'nullable|boolean',
        ]);

        $data['show_logo'] = $request->boolean('show_logo');

        InvoiceSetting::updateOrCreate(['tenant_id' => $tenant->id], $data);

        return back()->with('success', 'Invoice settings updated.');
    }

    public function updateBankDetails(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $data = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:11',
            'branch_name' => 'nullable|string|max:255',
            'upi_id' => 'nullable|string|max:100',
        ]);

        // make all non-primary
        BankDetail::where('tenant_id', $tenant->id)->update(['is_primary' => false]);

        BankDetail::updateOrCreate(
            ['tenant_id' => $tenant->id, 'account_number' => $data['account_number']],
            array_merge($data, ['tenant_id' => $tenant->id, 'is_primary' => true])
        );

        return back()->with('success', 'Bank details updated.');
    }

    public function updateNotifications(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $data = $request->validate([
            'email_on_invoice_sent' => 'nullable|boolean',
            'email_on_payment_received' => 'nullable|boolean',
            'email_on_invoice_overdue' => 'nullable|boolean',
            'whatsapp_on_invoice_sent' => 'nullable|boolean',
            'whatsapp_on_payment_received' => 'nullable|boolean',
            'whatsapp_on_invoice_overdue' => 'nullable|boolean',
            'reminder_days_before_due' => 'required|integer|min:0|max:30',
            'reminder_frequency' => 'required|in:once,daily,weekly',
        ]);

        foreach ([
            'email_on_invoice_sent','email_on_payment_received','email_on_invoice_overdue',
            'whatsapp_on_invoice_sent','whatsapp_on_payment_received','whatsapp_on_invoice_overdue'
        ] as $k) {
            $data[$k] = $request->boolean($k);
        }

        NotificationSetting::updateOrCreate(['tenant_id' => $tenant->id], $data);

        return back()->with('success', 'Notification settings updated.');
    }

public function updatePaymentGateway(Request $request)
{
    $tenant = auth()->user()->tenant;

    $data = $request->validate([
        'razorpay_enabled' => 'nullable|boolean',
        'razorpay_key_id' => 'nullable|string|max:255',
        'razorpay_key_secret' => 'nullable|string|max:500',
        'razorpay_webhook_secret' => 'nullable|string|max:500',

        'stripe_enabled' => 'nullable|boolean',
        'stripe_publishable_key' => 'nullable|string|max:255',
        'stripe_secret_key' => 'nullable|string|max:500',
    ]);

    $data['razorpay_enabled'] = $request->boolean('razorpay_enabled');
    $data['stripe_enabled'] = $request->boolean('stripe_enabled');

    // IMPORTANT: do NOT overwrite existing secrets if blank
    if (!$request->filled('razorpay_key_secret')) {
        unset($data['razorpay_key_secret']);
    } else {
        $data['razorpay_key_secret'] = \Crypt::encryptString($data['razorpay_key_secret']);
    }

    if (!$request->filled('razorpay_webhook_secret')) {
        unset($data['razorpay_webhook_secret']);
    } else {
        $data['razorpay_webhook_secret'] = \Crypt::encryptString($data['razorpay_webhook_secret']);
    }

    if (!$request->filled('stripe_secret_key')) {
        unset($data['stripe_secret_key']);
    } else {
        $data['stripe_secret_key'] = \Crypt::encryptString($data['stripe_secret_key']);
    }

    \App\Models\PaymentGatewaySetting::updateOrCreate(
        ['tenant_id' => $tenant->id],
        $data
    );

    return back()->with('success', 'Payment gateway settings saved.');
}

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated.');
    }

    public function export(string $type)
    {
        $tenantId = auth()->user()->tenant_id;

        if ($type === 'brands') {
            $rows = Brand::orderBy('name')->get()->map(fn($b) => [
                'Name' => $b->name,
                'Email' => $b->email ?? '',
                'Phone' => $b->phone ?? '',
                'GSTIN' => $b->gstin ?? '',
                'State Code' => $b->state_code ?? '',
                'Status' => $b->status,
            ]);
            return $this->csv('brands', $rows);
        }

        if ($type === 'invoices') {
            $rows = Invoice::with('brand')->latest('issue_date')->get()->map(fn($i) => [
                'Invoice' => $i->invoice_number,
                'Brand' => $i->brand->name ?? '',
                'Issue Date' => $i->issue_date->toDateString(),
                'Due Date' => $i->due_date->toDateString(),
                'Status' => $i->status,
                'Total' => $i->total_amount,
                'Net' => $i->net_receivable,
                'Paid' => $i->amount_paid,
                'Due' => $i->amount_due,
            ]);
            return $this->csv('invoices', $rows);
        }

        if ($type === 'payments') {
            $rows = Payment::with('invoice.brand')->latest('payment_date')->get()->map(fn($p) => [
                'Date' => $p->payment_date->toDateString(),
                'Amount' => $p->amount,
                'Method' => $p->payment_method,
                'Invoice' => $p->invoice->invoice_number ?? '',
                'Brand' => $p->invoice->brand->name ?? '',
                'Status' => $p->status,
                'Reference' => $p->transaction_reference ?? '',
            ]);
            return $this->csv('payments', $rows);
        }

        return back()->with('error', 'Invalid export type.');
    }

    private function csv(string $name, $rows)
    {
        $rows = collect($rows);
        if ($rows->isEmpty()) {
            $csv = "No data\n";
            return response($csv)->header('Content-Type','text/csv')
                ->header('Content-Disposition', "attachment; filename=\"{$name}-" . date('Y-m-d') . ".csv\"");
        }

        $headers = array_keys($rows->first());
        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($v) {
                $v = (string) $v;
                $v = str_replace('"','""',$v);
                return "\"{$v}\"";
            }, array_values($row))) . "\n";
        }

        return response($csv)->header('Content-Type','text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$name}-" . date('Y-m-d') . ".csv\"");
    }
}