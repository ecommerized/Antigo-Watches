<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Branch extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'coverage' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['image_fullpath'];

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function getImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;
        $path = asset('public/assets/admin/img/160x160/img2.jpg');

        if (!is_null($image) && Storage::disk('public')->exists('branch/' . $image)) {
            $path = asset('storage/app/public/branch/' . $image);
        }
        return $path;
    }

    public function delivery_charge_setup()
    {
        return $this->hasOne(DeliveryChargeSetup::class, 'branch_id', 'id');
    }

    public function delivery_charge_by_area()
    {
        return $this->hasMany(DeliveryChargeByArea::class, 'branch_id', 'id')->latest();
    }
}
