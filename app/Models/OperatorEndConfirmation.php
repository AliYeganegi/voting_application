<?php

// app/Models/OperatorEndConfirmation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorEndConfirmation extends Model
{
    protected $fillable = ['request_id','operator_id'];
}

