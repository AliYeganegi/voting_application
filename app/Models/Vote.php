<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = [
        'hashed_voter_id',
        'candidate_id',
        'voting_session_id'
    ];

    public function votingSession()
    {
        return $this->belongsTo(VotingSession::class);
    }
}
