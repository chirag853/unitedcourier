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
        'surcharge_id',
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
        'surcharge_id' => 'array',
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
        $surcharges = $this->surcharge_amount;
        $gst = (float) $this->gst_amount > 0
            ? (float) $this->gst_amount
            : (($base + $fuel + $surcharges) * (float) $this->gst_percentage / 100);

        return round($base + $fuel + $gst + $surcharges, 2);
    }

    /**
     * Normalize surcharge_id into a clean list of integer ids.
     * Handles JSON-array strings ("[1,2]"), plain CSV ("1,2") and real arrays.
     */
    protected function parseSurchargeIds(): array
    {
        $ids = $this->surcharge_id;

        if (is_string($ids)) {
            $ids = trim($ids);
            if ($ids === '' || $ids === 'null' || $ids === '[]') {
                return [];
            }
            $decoded = json_decode($ids, true);
            if (is_array($decoded)) {
                $ids = $decoded;
            } else {
                $ids = explode(',', $ids);
            }
        }

        if (!is_array($ids) || empty($ids)) {
            return [];
        }

        return array_values(array_filter(array_map('intval', $ids)));
    }

    /**
     * Get the total price of all surcharges attached to this rate.
     */
    public function getSurchargeAmountAttribute(): float
    {
        $ids = $this->parseSurchargeIds();

        return empty($ids)
            ? 0.0
            : round((float) SurCharge::whereIn('id', $ids)->sum('price'), 2);
    }

    /**
     * Get the surcharge records attached to this rate.
     */
    public function surchargeModels()
    {
        $ids = $this->parseSurchargeIds();

        return empty($ids)
            ? collect()
            : SurCharge::whereIn('id', $ids)->get();
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