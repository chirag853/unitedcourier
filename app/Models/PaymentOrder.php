<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    protected $table = 'payment_orders';

    protected $fillable = [
        'customer_id',
        'cashfree_order_id',
        'order_amount',
        'currency',
        'status',
        'payment_session_id',
        'recharge_type',
        'cf_payment_id',
        'payment_method',
        'payment_time',
        'verified_at',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'payment_time' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
