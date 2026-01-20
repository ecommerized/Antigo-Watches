<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;
    protected $fillable = ['wallet_balance'];

    public function accountable()
    {
        return $this->morphTo();
    }
}
