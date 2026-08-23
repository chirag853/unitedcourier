<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentTracking extends Model
{
    use HasFactory;

    protected $table = 'shipment_tracking';

    protected $fillable = [
        'customer_id',
        'shipper_id',
        'create_shipment_id',
        'response_status_code',
        'response_status_description',
        'transaction_identifier',
        'customer_context',
        'shipment_identification_number',
        'transportation_charges_currency',
        'transportation_charges_amount',
        'service_options_charges_currency',
        'service_options_charges_amount',
        'total_charges_currency',
        'total_charges_amount',
        'billing_weight_uom',
        'billing_weight',
        'package_results',
        'raw_response',
        'status',
        'tracking_provider',
        'tracking_status',
        'tracking_response',
        'tracking_error',
        'tracking_synced_at',
    ];

    protected $casts = [
        'transportation_charges_amount' => 'decimal:2',
        'service_options_charges_amount' => 'decimal:2',
        'total_charges_amount' => 'decimal:2',
        'billing_weight' => 'decimal:2',
        'package_results' => 'array',
        'raw_response' => 'array',
        'tracking_response' => 'array',
        'tracking_synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns this tracking record.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the shipper info for this tracking record.
     */
    public function shipperInfo()
    {
        return $this->belongsTo(ShipperInfo::class, 'shipper_id');
    }

    /**
     * Get the create shipment record.
     */
    public function createShipment()
    {
        return $this->belongsTo(CreateShipment::class, 'create_shipment_id');
    }
}
