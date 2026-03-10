<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadmapItem extends Model
{
    protected $fillable = [
        'title', 'description', 'category', 'status', 'priority',
        'target_quarter', 'votes_count',
    ];

    public function votes() { return $this->hasMany(RoadmapVote::class); }
}