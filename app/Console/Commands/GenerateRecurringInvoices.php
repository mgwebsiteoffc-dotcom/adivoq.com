<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Tenant;

class GenerateRecurringInvoices extends Command
{
    protected $signature = 'invoices:generate-recurring {--dry-run}';
    protected $description = 'Generate invoices from recurring invoice templates';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $today = now()->toDateString();

        $templates = Invoice::withoutGlobalScopes()
            ->where('is_recurring', true)
            ->where('paused', false)
            ->whereNotNull('next_recurring_date')
            ->whereDate('next_recurring_date', '<=', $today)
            ->whereNotIn('status', ['cancelled'])
            ->with(['items', 'tenant', 'brand'])
            ->get();

        if ($dry) {
            $this->info("[DRY RUN] Would generate from {$templates->count()} templates.");
            return self::SUCCESS;
        }

        $generated = 0;
        foreach ($templates as $template) {
            $tenant = $template->tenant;

            if (!$tenant || $tenant->status !== 'active') {
                continue;
            }

            // Respect plan limits (important)
            if (!$tenant->canCreateInvoice()) {
                $template->logActivity('recurring_skipped', 'Skipped recurring generation due to plan limit');
                continue;
            }

            DB::beginTransaction();
            try {
                $settings = $tenant->invoiceSetting;
                $newNumber = $settings ? $settings->generateInvoiceNumber() : ('INV-' . str_pad(rand(1,99999), 5, '0', STR_PAD_LEFT));

                $issueDate = Carbon::parse($template->next_recurring_date);
                $dueDays = $template->payment_terms_days ?? ($settings->default_payment_terms_days ?? 30);
                $dueDate = $issueDate->copy()->addDays($dueDays);

                $newInvoice = $template->replicate([
                    'invoice_number',
                    'status',
                    'issue_date',
                    'due_date',
                    'amount_paid',
                    'amount_due',
                    'sent_at',
                    'viewed_at',
                    'paid_at',
                    'cancelled_at',
                    'payment_link_token',
                    'payment_link_expires_at',
                ]);

                $newInvoice->invoice_number = $newNumber;
                $newInvoice->status = 'draft';
                $newInvoice->issue_date = $issueDate->toDateString();
                $newInvoice->due_date = $dueDate->toDateString();
                $newInvoice->amount_paid = 0;
                $newInvoice->amount_due = 0;
                $newInvoice->sent_at = null;
                $newInvoice->viewed_at = null;
                $newInvoice->paid_at = null;
                $newInvoice->cancelled_at = null;
                $newInvoice->payment_link_token = null;
                $newInvoice->payment_link_expires_at = null;

                // Important: the generated invoice should NOT itself be recurring
                $newInvoice->is_recurring = false;
                $newInvoice->recurring_frequency = null;
                $newInvoice->next_recurring_date = null;

                $newInvoice->save();

                foreach ($template->items as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $newInvoice->id,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'amount' => $item->amount,
                        'hsn_sac_code' => $item->hsn_sac_code,
                        'tax_rate' => $item->tax_rate,
                        'sort_order' => $item->sort_order,
                    ]);
                }

                $newInvoice->calculateTotals();
                $newInvoice->logActivity('created', 'Generated from recurring template', [
                    'template_invoice_id' => $template->id,
                    'template_invoice_number' => $template->invoice_number,
                ]);

                // update tenant usage
                $tenant->increment('monthly_invoice_count');

                // update next date on template
                $template->update([
                    'next_recurring_date' => $this->nextDate($template->next_recurring_date, $template->recurring_frequency),
                ]);

                $template->logActivity('recurring_generated', "Generated recurring invoice {$newNumber}", [
                    'generated_invoice_id' => $newInvoice->id,
                    'generated_invoice_number' => $newNumber,
                ]);

                DB::commit();
                $generated++;
            } catch (\Exception $e) {
                DB::rollBack();
                $template->logActivity('recurring_error', 'Recurring generation error: ' . $e->getMessage());
            }
        }

        $this->info("Generated recurring invoices: {$generated}");
        return self::SUCCESS;
    }

    private function nextDate(string $date, ?string $freq): string
    {
        $d = Carbon::parse($date);

        return match ($freq) {
            'monthly' => $d->addMonthNoOverflow()->toDateString(),
            'quarterly' => $d->addMonthsNoOverflow(3)->toDateString(),
            'yearly' => $d->addYearNoOverflow()->toDateString(),
            default => $d->addMonthNoOverflow()->toDateString(),
        };
    }
}