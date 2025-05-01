<?php
// app/Models/Verification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'voting_session_id',
        'voter_id',
        'voter_hash',
        'started_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];


    public function session()
    {
        return $this->belongsTo(VotingSession::class);
    }
}
