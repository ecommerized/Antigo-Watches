<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'walletable_id',
        'walletable_type',
        'transaction_id',
        'type',
        'direction',
        'amount',
        'opening_balance',
        'closing_balance',
        'reference',
        'method',
        'description',
        'status',
    ];

    public function walletable()
    {
       return $this->morphTo();
    }

    public function scopeFilter($query, $filters)
    {
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }
        if (!empty($filters['transaction_type']) && ($filters['transaction_type'] == 'credit' || $filters['transaction_type'] == 'debit') ) {
            $query->where('direction', $filters['transaction_type']);
        }
        if (!empty($filters['customer_id']) && $filters['customer_id'] != 'all') {
            $query->where('walletable_id', $filters['customer_id'])
                ->where('walletable_type', User::class);
        }
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = self::generateUniqueTransactionId();
            }
        });
    }

    public static function generateUniqueTransactionId()
    {
        do {
            // Example format: TXN-APR282025-AB12CD34
            $transactionId = 'TXN-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));
        } while (self::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
}
