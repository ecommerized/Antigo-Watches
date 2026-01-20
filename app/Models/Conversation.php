<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Conversation extends Model
{
    protected $casts = [
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
                if (Storage::disk('public')->exists('conversation/' . $item)) {
                    $imageUrlArray[$key] = asset('storage/app/public/conversation/'. $item) ;
                } else {
                    $imageUrlArray[$key] = asset('public/assets/admin/img/900x400/img1.jpg');
                }
            }
        }
        return $imageUrlArray;
    }
}
