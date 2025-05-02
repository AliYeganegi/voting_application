<?php

// app/Models/OperatorApproval.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorApproval extends Model
{
    protected $fillable = [
      'voting_session_id',
      'operator_id',
      'action',
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
