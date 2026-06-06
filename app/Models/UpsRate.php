<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsRate extends Model
{
    use HasFactory;

    protected $table = 'united_customer_rate';

    protected $fillable = [
        'customer_id',
        'network',
        'service',
        'type',
        'method',
        'expected_delivery',
        'weight_start_gm',
        'weight_end_gm',
        'zone_id',
        'rate',
        'default_rate',
    ];

    protected $casts = [
        'weight_start_gm' => 'decimal:3',
        'weight_end_gm' => 'decimal:3',
        'rate' => 'decimal:2',
        'default_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the zone that this rate belongs to.
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    /**
     * Get the customer that this rate belongs to.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}