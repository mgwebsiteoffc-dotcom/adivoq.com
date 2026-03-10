<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'tenant_id', 'email_on_invoice_sent', 'email_on_payment_received',
        'email_on_invoice_overdue', 'whatsapp_on_invoice_sent',
        'whatsapp_on_payment_received', 'whatsapp_on_invoice_overdue',
        'reminder_days_before_due', 'reminder_frequency',
    ];

    protected $casts = [
        'email_on_invoice_sent' => 'boolean',
        'email_on_payment_received' => 'boolean',
        'email_on_invoice_overdue' => 'boolean',
        'whatsapp_on_invoice_sent' => 'boolean',
        'whatsapp_on_payment_received' => 'boolean',
        'whatsapp_on_invoice_overdue' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
}