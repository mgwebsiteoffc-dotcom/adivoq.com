<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'logo', 'business_name',
        'address_line1', 'address_line2', 'city', 'state', 'state_code',
        'pincode', 'country', 'pan_number', 'gstin', 'gst_registered',
        'plan', 'pending_plan', 'pending_plan_effective_at', 'plan_status',
        'trial_ends_at', 'subscription_ends_at',
        'monthly_invoice_count', 'invoice_count_reset_at', 'status',
    ];

    protected $casts = [
        'gst_registered' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'pending_plan_effective_at' => 'datetime',
        'invoice_count_reset_at' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name) . '-' . Str::random(5);
            }
        });
    }

    // Relationships
    public function users() { return $this->hasMany(User::class); }
    public function owner() { return $this->hasOne(User::class)->where('role', 'owner'); }
    public function brands() { return $this->hasMany(Brand::class); }
    public function campaigns() { return $this->hasMany(Campaign::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function expenses() { return $this->hasMany(Expense::class); }
    public function taxSetting() { return $this->hasOne(TaxSetting::class); }
    public function bankDetails() { return $this->hasMany(BankDetail::class); }
    public function primaryBank() { return $this->hasOne(BankDetail::class)->where('is_primary', true); }
    public function invoiceSetting() { return $this->hasOne(InvoiceSetting::class); }
    public function notificationSetting() { return $this->hasOne(NotificationSetting::class); }
    public function paymentGatewaySetting() { return $this->hasOne(PaymentGatewaySetting::class); }
    public function teamInvitations() { return $this->hasMany(TeamInvitation::class); }
    public function subscriptionPayments() { return $this->hasMany(SubscriptionPayment::class); }
    public function trackingKeys() { return $this->hasMany(TrackingKey::class); }
    public function whatsappChatbots() { return $this->hasMany(WhatsAppChatbotConfig::class); }
    public function services() { return $this->hasMany(TenantService::class); }

    // Helpers
    public function isActive(): bool { return $this->status === 'active'; }
    public function isOnTrial(): bool { return $this->plan_status === 'trial' && $this->trial_ends_at?->isFuture(); }

    public function getPlanConfig(): array
    {
        return config('invoicehero.plans.' . $this->plan, []);
    }

    public function canCreateInvoice(): bool
    {
        $limit = $this->getPlanConfig()['invoices_per_month'] ?? 5;
        if ($limit === -1) return true;
        return $this->monthly_invoice_count < $limit;
    }

    public function canAddBrand(): bool
    {
        $limit = $this->getPlanConfig()['brands'] ?? 3;
        if ($limit === -1) return true;
        return $this->brands()->count() < $limit;
    }

    public function hasFeature(string $feature): bool
    {
        $features = $this->getPlanConfig()['features'] ?? [];
        return in_array('all', $features) || in_array($feature, $features);
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line1, $this->address_line2,
            $this->city, $this->state, $this->pincode, $this->country
        ])->filter()->implode(', ');
    }
}