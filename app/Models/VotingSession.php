<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingSession extends Model
{
    protected $fillable = ['start_at', 'end_at', 'is_active'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
