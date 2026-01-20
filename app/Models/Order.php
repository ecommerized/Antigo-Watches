<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'is_guest',
        'order_amount',
        'coupon_discount_amount',
        'coupon_discount_title',
        'payment_status',
        'order_status',
        'total_tax_amount',
        'payment_method',
        'additional_payment_method',
        'additional_payment_amount',
        'transaction_reference',
        'delivery_address_id',
        'created_at',
        'updated_at',
        'checked',
        'delivery_man_id',
        'delivery_charge',
        'order_note',
        'coupon_code',
        'order_type',
        'branch_id',
        'callback',
        'extra_discount',
        'delivery_address',
        'bring_change_amount',
        'paid_amount',
        'wallet',
        'additional_payment_amount',
    ];

    protected $casts = [
        'order_amount' => 'float',
        'coupon_discount_amount' => 'float',
        'total_tax_amount' => 'float',
        'delivery_address_id' => 'integer',
        'delivery_man_id' => 'integer',
        'delivery_charge' => 'float',
        'user_id' => 'integer',
        'delivery_address' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'bring_change_amount' => 'float',
        'paid_amount' => 'float',
        'additional_payment_method' => 'array',
        'additional_payment_amount' => 'array',
    ];

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function delivery_man(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id');
    }

    public function scopePos($query)
    {
        return $query->where('order_type', '=', 'pos');
    }

    public function scopeNotPos($query)
    {
        return $query->where('order_type', '!=', 'pos');
    }

    public function guest()
    {
        return $this->belongsTo(GuestUser::class, 'user_id');
    }

    public function orderArea() {
        return $this->belongsTo(OrderArea::class, 'area_id');
    }

    public static function booted()
    {
        static::creating(function ($order) {
            // Generate a custom Order ID
            if (!$order->id) {
                // If order_id needs to follow a custom format like ORD-XXXX, based on the last inserted order
                $lastOrder = self::latest('id')->first();
                $order->id = $lastOrder ? $lastOrder->id + 1 : 100001; // Defaulting to 100000 if no last order exists
            }
        });
    }
}
