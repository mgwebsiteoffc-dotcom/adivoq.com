<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadmapVote extends Model
{
    public $timestamps = false;
    protected $fillable = ['roadmap_item_id', 'ip_address', 'session_id'];
    protected $casts = ['created_at' => 'datetime'];

    public function item() { return $this->belongsTo(RoadmapItem::class, 'roadmap_item_id'); }
}