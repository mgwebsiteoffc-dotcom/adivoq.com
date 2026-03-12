<?php
// app/Models/Waitlist.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    use HasFactory;

    protected $table = 'waitlist';

    protected $fillable = [
        'name',
        'email',
        'creator_type',
        'followers_count',
        'source',
        'status',
    ];
}