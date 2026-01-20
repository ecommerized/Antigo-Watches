<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcConversation extends Model
{
    protected $casts = [
        'order_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }
}
