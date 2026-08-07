<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipperInfo extends Model
{
    use HasFactory;

    protected $table = 'shipper_info';

    protected $fillable = [
        'customer_id',
        'awb_number',
        'shipping_method',
        'shipper_same_as_customer',
        'company_name',
        'contact_person',
        'address_line1',
        'address_line2',
        'address_line3',
        'pincode',
        'city',
        'state',
        'phone_number',
        'email',
        'email_opt_out',
        'kyc_type',
        'kyc_number',
        'service_rate_id',
        'service_id',
        'status',
    ];

    protected $casts = [
        'shipper_same_as_customer' => 'boolean',
        'email_opt_out' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the shipments for this shipper.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'shipper_id');
    }

    /**
     * Get the consignee info for this shipper.
     */
    public function consigneeInfo()
    {
        return $this->hasOne(ConsigneeInfo::class, 'shipper_id');
    }

    /**
     * Get the shipment tracking for this shipper.
     */
    public function shipmentTracking()
    {
        return $this->hasOne(ShipmentTracking::class, 'shipper_id');
    }

    /**
     * Get the package dimensions for this shipper.
     */
    public function packageDimensions()
    {
        return $this->hasMany(PackageDimension::class, 'shipper_id');
    }

    /**
     * Get the selected courier service rate for this shipper.
     */
    public function serviceRate()
    {
        return $this->belongsTo(CourierRate::class, 'service_rate_id');
    }

    /**
     * Get the CSB information for this shipper.
     */
    public function csbInformation()
    {
        return $this->hasOne(CsbInformation::class, 'shipper_id');
    }

    /**
     * Get the shipment invoices for this shipper.
     */
    public function invoices()
    {
        return $this->hasMany(ShipmentInvoice::class, 'shipper_id');
    }

    /**
     * Get the tracking records for this shipper.
     */
    public function trackingRecords()
    {
        return $this->hasMany(Tracking::class, 'shipper_id');
    }
}
