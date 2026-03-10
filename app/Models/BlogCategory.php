<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($cat) => $cat->slug = $cat->slug ?: Str::slug($cat->name));
    }

    public function posts() { return $this->hasMany(BlogPost::class); }
}