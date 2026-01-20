<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $casts = [
        'email' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
