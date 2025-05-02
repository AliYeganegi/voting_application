<?php

// app/Models/OperatorStartConfirmation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorStartConfirmation extends Model
{
    protected $fillable = ['request_id','operator_id'];
}
