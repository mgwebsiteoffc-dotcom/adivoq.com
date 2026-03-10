<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Guide extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'admin_user_id', 'title', 'slug', 'description', 'cover_image',
        'category', 'status', 'published_at',
    ];

    protected $casts = ['published_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($g) => $g->slug = $g->slug ?: Str::slug($g->title));
    }

    public function author() { return $this->belongsTo(AdminUser::class, 'admin_user_id'); }
    public function steps() { return $this->hasMany(GuideStep::class)->orderBy('sort_order'); }

    public function scopePublished($query) {
        return $query->where('status', 'published');
    }
}