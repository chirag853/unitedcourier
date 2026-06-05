<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierRate extends Model
{
    use HasFactory;

    protected $table = 'courier_rates';

    protected $fillable = [
        'customer_id',
        'service_id',
        'wt_range_start',
        'wt_range_end',
        'zone_no',
        'price',
    ];

    protected $casts = [
        'wt_range_start' => 'decimal:3',
        'wt_range_end' => 'decimal:3',
        'price' => 'decimal:2',
    ];

    /**
     * Get the courier service that this rate belongs to.
     */
    public function service()
    {
        return $this->belongsTo(CourierService::class, 'service_id');
    }

    /**
     * Get the customer that this rate belongs to.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}