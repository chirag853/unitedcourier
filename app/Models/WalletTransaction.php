<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wallet_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'customer_id',
        'type',
        'reason',
        'amount',
        'balance_after',
        'reference',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount'        => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /**
     * Boot the model.
     *
     * Automatically generates a unique, human-readable transaction_id
     * (format: WT-YYYYMMDD-XXXXXX) for every new wallet transaction.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (WalletTransaction $transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = $transaction->generateTransactionId();
            }
        });
    }

    /**
     * Generate a unique transaction ID.
     * Format: WT-YYYYMMDD-XXXXXX
     */
    public function generateTransactionId(): string
    {
        $prefix = 'WT-' . now()->format('Ymd') . '-';

        do {
            $id = $prefix . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('transaction_id', $id)->exists());

        return $id;
    }

    /**
     * Get the customer that owns this transaction.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Scope a query to only credit transactions.
     */
    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    /**
     * Scope a query to only debit transactions.
     */
    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }
}
