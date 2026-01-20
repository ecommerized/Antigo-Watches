<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    use HasFactory;

    protected $table = 'flash_sales';

    protected $casts = [
        'status' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FlashSaleProduct::class, 'flash_sale_id');
    }

    public function scopeActive($query)
    {
        return $query->where(['status' => 1])->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('end_date', '>=', date('Y-m-d'));
    }
}
