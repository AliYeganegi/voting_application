<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingSession extends Model
{
    protected $fillable = ['start_at', 'end_at', 'is_active', 'result_file'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function startApprovals()
    {
        return $this->hasMany(OperatorApproval::class)
            ->where('action', 'start');
    }

    public function endApprovals()
    {
        return $this->hasMany(OperatorApproval::class)
            ->where('action', 'end');
    }
}
