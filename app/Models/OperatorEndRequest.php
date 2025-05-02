<?php

// app/Models/OperatorEndRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorEndRequest extends Model
{
    protected $fillable = ['session_id','status'];

    public function confirmations()
    {
        return $this->hasMany(OperatorEndConfirmation::class, 'request_id');
    }

    public function session()
    {
        return $this->belongsTo(VotingSession::class, 'session_id');
    }

    public function scopePending($q)
    {
        return $q->where('status','pending');
    }
}

