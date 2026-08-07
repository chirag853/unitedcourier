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
        'fuel_charge',
        'fuel_percentage',
        'gst_percentage',
        'gst_amount',
        'is_default',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'wt_range_start' => 'decimal:3',
        'wt_range_end' => 'decimal:3',
        'price' => 'decimal:2',
        'fuel_charge' => 'decimal:2',
        'fuel_percentage' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'is_default' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the complete shipping charge, including fuel and GST.
     *
     * Stored fixed charges take precedence over percentage-based charges.
     */
    public function getInclusiveTotalAttribute(): float
    {
        $base = (float) $this->price;
        $fuel = (float) $this->fuel_charge > 0
            ? (float) $this->fuel_charge
            : ($base * (float) $this->fuel_percentage / 100);
        $gst = (float) $this->gst_amount > 0
            ? (float) $this->gst_amount
            : (($base + $fuel) * (float) $this->gst_percentage / 100);

        return round($base + $fuel + $gst, 2);
    }

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