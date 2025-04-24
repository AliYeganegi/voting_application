<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportFile extends Model
{
    protected $fillable = ['type', 'original_name', 'path'];
}

