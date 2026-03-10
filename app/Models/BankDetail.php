<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $fillable = [
        'tenant_id', 'bank_name', 'account_holder_name', 'account_number',
        'ifsc_code', 'branch_name', 'upi_id', 'is_primary',
    ];

    protected $casts = ['is_primary' => 'boolean'];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    public function getMaskedAccountAttribute(): string
    {
        return str_repeat('X', max(0, strlen($this->account_number) - 4))
            . substr($this->account_number, -4);
    }
}