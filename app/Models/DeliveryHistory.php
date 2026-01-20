<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    protected $casts = [
        'order_id' => 'integer',
        'deliveryman_id' => 'integer',
        'time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $fillable = [
        'order_id',
        'deliveryman_id',
        'time',
        'location',
        'latitude',
        'longitude',
        'created_at',
        'updated_at'
    ];
}
