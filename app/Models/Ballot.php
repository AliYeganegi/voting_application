<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ballot extends Model
{
    protected $fillable = [
        'voting_session_id',
        'voter_hash',
    ];

    /**
     * The session this ballot belongs to.
     */
    public function session()
    {
        return $this->belongsTo(VotingSession::class, 'voting_session_id');
    }

    /**
     * The candidates chosen on this ballot.
     */
    public function candidates()
    {
        return $this->belongsToMany(
            User::class,
            'ballot_candidate',
            'ballot_id',
            'candidate_id'
        );
    }
}
