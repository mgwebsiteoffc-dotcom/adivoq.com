<?php
// app/Helpers/helpers.php

use Illuminate\Support\Str;

if (!function_exists('format_currency')) {
    /**
     * Format amount as currency
     */
    function format_currency($amount, $currency = 'INR')
    {
        $symbols = config('adivoq.currency.symbols');
        $symbol = $symbols[$currency] ?? $currency . ' ';

        return $symbol . number_format($amount, 2);
    }
}

if (!function_exists('generate_invoice_number')) {
    /**
     * Generate next invoice number for tenant
     */
    function generate_invoice_number($tenantId, $prefix = null)
    {
        $prefix = $prefix ?? config('adivoq.invoice.default_prefix');
        $year = now()->year;

        $lastInvoice = \App\Models\Invoice::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;
        $paddedNumber = str_pad($number, config('adivoq.invoice.number_padding'), '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$paddedNumber}";
    }
}

if (!function_exists('generate_public_token')) {
    /**
     * Generate random public token
     */
    function generate_public_token($length = 32)
    {
        return Str::random($length);
    }
}

if (!function_exists('calculate_gst')) {
    /**
     * Calculate GST breakdown
     *
     * @param float $amount Taxable amount
     * @param float $rate GST rate (18)
     * @param string $type 'igst' or 'cgst_sgst'
     * @return array
     */
    function calculate_gst($amount, $rate = 18, $type = 'igst')
    {
        $gstAmount = ($amount * $rate) / 100;

        if ($type === 'cgst_sgst') {
            return [
                'cgst_rate' => $rate / 2,
                'cgst_amount' => $gstAmount / 2,
                'sgst_rate' => $rate / 2,
                'sgst_amount' => $gstAmount / 2,
                'igst_rate' => 0,
                'igst_amount' => 0,
                'total_gst' => $gstAmount,
            ];
        }

        return [
            'cgst_rate' => 0,
            'cgst_amount' => 0,
            'sgst_rate' => 0,
            'sgst_amount' => 0,
            'igst_rate' => $rate,
            'igst_amount' => $gstAmount,
            'total_gst' => $gstAmount,
        ];
    }
}

if (!function_exists('calculate_tds')) {
    /**
     * Calculate TDS amount
     */
    function calculate_tds($amount, $rate = 10)
    {
        return ($amount * $rate) / 100;
    }
}

if (!function_exists('indian_state_code')) {
    /**
     * Get Indian state code from state name
     */
    function indian_state_code($stateName)
    {
        $states = [
            'Andhra Pradesh' => '37',
            'Arunachal Pradesh' => '12',
            'Assam' => '18',
            'Bihar' => '10',
            'Chhattisgarh' => '22',
            'Goa' => '30',
            'Gujarat' => '24',
            'Haryana' => '06',
            'Himachal Pradesh' => '02',
            'Jharkhand' => '20',
            'Karnataka' => '29',
            'Kerala' => '32',
            'Madhya Pradesh' => '23',
            'Maharashtra' => '27',
            'Manipur' => '14',
            'Meghalaya' => '17',
            'Mizoram' => '15',
            'Nagaland' => '13',
            'Odisha' => '21',
            'Punjab' => '03',
            'Rajasthan' => '08',
            'Sikkim' => '11',
            'Tamil Nadu' => '33',
            'Telangana' => '36',
            'Tripura' => '16',
            'Uttar Pradesh' => '09',
            'Uttarakhand' => '05',
            'West Bengal' => '19',
            'Delhi' => '07',
        ];

        return $states[$stateName] ?? null;
    }
}

if (!function_exists('log_activity')) {
    /**
     * Log activity
     */
    function log_activity($action, $description, $loggable = null, $oldValues = null, $newValues = null)
    {
        \App\Models\ActivityLog::create([
            'tenant_id' => session('tenant_id'),
            'user_id' => auth()->id(),
            'loggable_type' => $loggable ? get_class($loggable) : null,
            'loggable_id' => $loggable?->id,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

if (!function_exists('getRolePermissions')) {
    function getRolePermissions($role)
    {
        $map = [
            'manager' => [
                'view_revenue' => true,
                'edit_invoice' => true,
                'delete_expense' => true,
                'manage_team' => true,
            ],
            'accountant' => [
                'view_revenue' => true,
                'edit_invoice' => true,
                'delete_expense' => false,
                'manage_team' => false,
            ],
            'editor' => [
                'view_revenue' => false,
                'edit_invoice' => true,
                'delete_expense' => false,
                'manage_team' => false,
            ],
            'viewer' => [
                'view_revenue' => false,
                'edit_invoice' => false,
                'delete_expense' => false,
                'manage_team' => false,
            ],
        ];
        return $map[$role] ?? [];
    }
}