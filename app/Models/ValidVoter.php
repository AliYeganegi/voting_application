<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidVoter extends Model
{
    protected $fillable = [
        'voter_id', 'first_name', 'last_name', 'license_number', 'has_voted'
    ];

}
