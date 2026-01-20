<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DMReview extends Model
{
    protected $casts = [
        'delivery_man_id' => 'integer',
        'order_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['attachment_fullpath'];

    public function getAttachmentFullPathAttribute()
    {
        $value = $this->attachment ?? [];
        $imageUrlArray = is_array($value) ? $value : json_decode($value, true);
        if (is_array($imageUrlArray)) {
            foreach ($imageUrlArray as $key => $item) {
                if (Storage::disk('public')->exists('review/' . $item)) {
                    $imageUrlArray[$key] = asset('storage/app/public/review/'. $item) ;
                } else {
                    $imageUrlArray[$key] = asset('public/assets/admin/img/400x400/img2.jpg');
                }
            }
        }
        return $imageUrlArray;
    }


    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function delivery_man(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeliveryMan::class,'delivery_man_id');
    }
}
