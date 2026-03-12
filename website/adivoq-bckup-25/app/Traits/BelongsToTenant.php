<?php
// app/Traits/BelongsToTenant.php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToTenant()
    {
        // Auto-scope all queries to current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (session()->has('tenant_id')) {
                $builder->where('tenant_id', session('tenant_id'));
            }
        });

        // Auto-fill tenant_id when creating
        static::creating(function (Model $model) {
            if (session()->has('tenant_id') && !$model->tenant_id) {
                $model->tenant_id = session('tenant_id');
            }
        });
    }

    /**
     * Relationship to tenant
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}