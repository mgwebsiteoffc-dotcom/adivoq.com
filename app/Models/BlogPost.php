<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'blog_category_id', 'admin_user_id', 'title', 'slug', 'excerpt',
        'content', 'cover_image', 'meta_title', 'meta_description',
        'status', 'published_at',
    ];

    protected $casts = ['published_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($post) => $post->slug = $post->slug ?: Str::slug($post->title));
    }

    public function category() { return $this->belongsTo(BlogCategory::class, 'blog_category_id'); }
    public function author() { return $this->belongsTo(AdminUser::class, 'admin_user_id'); }

    public function scopePublished($query) {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }
}