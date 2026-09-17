<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentDispute extends Model
{
    protected $table = 'shipment_disputes';

    protected $fillable = [
        'shipment_invoice_id',
        'shipper_id',
        'awb_number',
        'dispute_charge_id',
        'charge_type',
        'conditions',
        'destination',
        'service_id',
        'calculation_type',
        'values',
        'rate',
        'boxes',
        'base_amount',
        'gst_percentage',
        'gst_amount',
        'total_incl_gst',
        'currency',
        'applied_by',
        'status',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'total_incl_gst' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function disputeCharge()
    {
        return $this->belongsTo(DisputeCharge::class, 'dispute_charge_id');
    }
}
