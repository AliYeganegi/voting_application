<?php

// app/Models/OperatorStartRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorStartRequest extends Model
{
    protected $fillable = ['status'];

    public function confirmations()
    {
        return $this->hasMany(OperatorStartConfirmation::class, 'request_id');
    }

    public function scopePending($q)
    {
        return $q->where('status','pending');
    }
}
