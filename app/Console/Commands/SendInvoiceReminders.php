<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Services\WhatsAppService;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoices:send-reminders {--dry-run}';
    protected $description = 'Send upcoming due and overdue invoice reminders based on tenant notification settings';

    public function handle(): int
    {
        $lock = Cache::lock('invoices:send-reminders', 600);

        if (!$lock->get()) {
            $this->warn('Another reminder job is running. Exiting.');
            return self::SUCCESS;
        }

        try {
            $dry = (bool) $this->option('dry-run');
            $totalSent = 0;

            $tenants = Tenant::where('status', 'active')->with(['notificationSetting'])->get();

            foreach ($tenants as $tenant) {
                $ns = $tenant->notificationSetting;

                if (!$ns) continue;

                // If both channels are disabled, skip
                if (!$ns->email_on_invoice_overdue && !$ns->whatsapp_on_invoice_overdue) {
                    continue;
                }

                $daysBefore = (int) ($ns->reminder_days_before_due ?? 3);
                $frequency = $ns->reminder_frequency ?? 'once';

                // Invoices that are unpaid-ish
                $base = Invoice::withoutGlobalScopes()
                    ->where('tenant_id', $tenant->id)
                    ->whereIn('status', ['sent', 'viewed', 'partially_paid'])
                    ->with(['brand']);

                // A) Due soon bucket (due date within N days)
                $dueSoon = (clone $base)
                    ->whereDate('due_date', '=', now()->addDays($daysBefore)->toDateString())
                    ->get();

                // B) Overdue bucket
                $overdue = (clone $base)
                    ->whereDate('due_date', '<', now()->toDateString())
                    ->get();

                $sentForTenant = 0;

                foreach ($dueSoon as $inv) {
                    if (!$this->shouldSend($inv, $frequency)) continue;
                    $sentForTenant += $this->sendReminder($tenant, $inv, $ns, $dry, 'due_soon') ? 1 : 0;
                }

                foreach ($overdue as $inv) {
                    if (!$this->shouldSend($inv, $frequency)) continue;
                    $sentForTenant += $this->sendReminder($tenant, $inv, $ns, $dry, 'overdue') ? 1 : 0;
                }

                if ($sentForTenant > 0) {
                    $this->info("Tenant {$tenant->id} ({$tenant->name}): sent {$sentForTenant}");
                }

                $totalSent += $sentForTenant;
            }

            $this->info("Total reminders sent: {$totalSent}" . ($dry ? ' (dry-run)' : ''));

            return self::SUCCESS;
        } finally {
            optional($lock)->release();
        }
    }

    private function shouldSend(Invoice $invoice, string $frequency): bool
    {
        // Check last reminder activity to avoid spam
        $last = $invoice->activities()
            ->where('action', 'reminder_sent')
            ->latest('created_at')
            ->first();

        if (!$last) return true;

        if ($frequency === 'once') {
            return false; // already reminded once
        }

        if ($frequency === 'daily') {
            return $last->created_at->lt(now()->startOfDay());
        }

        if ($frequency === 'weekly') {
            return $last->created_at->lt(now()->subDays(7));
        }

        return true;
    }

    private function sendReminder($tenant, Invoice $invoice, $ns, bool $dry, string $kind): bool
    {
        $brand = $invoice->brand;

        // Make sure payment link exists (better UX)
        if (!$invoice->payment_link_token) {
            $invoice->generatePaymentLink();
        }

        $paymentLink = route('payment.link', $invoice->payment_link_token);

        $subject = "Payment Reminder: {$invoice->invoice_number}";
        $body = "Dear " . ($brand->contact_person ?? $brand->name) . ",\n\n"
            . "This is a reminder for invoice {$invoice->invoice_number}.\n"
            . "Amount Due: ₹" . number_format($invoice->amount_due, 2) . "\n"
            . "Due Date: " . $invoice->due_date->format('d M Y') . "\n"
            . "Pay here: {$paymentLink}\n\n"
            . "Thanks,\n" . ($tenant->business_name ?? $tenant->name);

        $sentAny = false;

        if ($ns->email_on_invoice_overdue && $brand->email) {
            if (!$dry) {
                Mail::raw($body, function ($m) use ($brand, $subject) {
                    $m->to($brand->email)->subject($subject);
                });
            }
            $sentAny = true;
        }

        if ($ns->whatsapp_on_invoice_overdue && $brand->phone && $tenant->hasFeature('whatsapp_sending')) {
            $whats = app(WhatsAppService::class);
            if (!$dry) {
                $whats->sendPaymentReminder($invoice, $brand->phone);
            }
            $sentAny = true;
        }

        if ($sentAny) {
            $invoice->logActivity('reminder_sent', "Reminder sent ({$kind})", [
                'kind' => $kind,
                'channels' => [
                    'email' => (bool) ($ns->email_on_invoice_overdue && $brand->email),
                    'whatsapp' => (bool) ($ns->whatsapp_on_invoice_overdue && $brand->phone),
                ],
            ]);
        }

        return $sentAny;
    }
}