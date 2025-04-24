<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = [
        'hashed_voter_id',
        'candidate_id',
    ];
}
