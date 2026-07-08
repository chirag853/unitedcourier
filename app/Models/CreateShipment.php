<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreateShipment extends Model
{
    use HasFactory;

    protected $table = 'create_shipment';

    protected $fillable = [
        'customer_id',
        'shipper_id',
        'awb_number',
        'delivery_destination',
        'origin_type',
        'shipping_method',
        'shipper_same_as_customer',
        'shipper_company_name',
        'shipper_contact_person',
        'shipper_address_line1',
        'shipper_address_line2',
        'shipper_address_line3',
        'shipper_pincode',
        'shipper_city',
        'shipper_state',
        'shipper_phone_number',
        'shipper_email',
        'shipper_email_opt_out',
        'shipper_kyc_type',
        'shipper_kyc_number',
        'consignee_name',
        'consignee_contact_person',
        'consignee_address_line1',
        'consignee_address_line2',
        'consignee_address_line3',
        'consignee_zip_code',
        'consignee_city',
        'consignee_state',
        'consignee_phone_number',
        'consignee_email',
        'consignee_email_opt_out',
        'invoice_number',
        'invoice_date',
        'invoice_amount',
        'incoterms',
        'invoice_currency',
        'reference_number',
        'ecommerce',
        'scheme',
        'bond_ut_igst',
        'lut_number',
        'iec_code',
        'gst_number',
        'ad_code',
        'bank_account_number',
        'bank_ifsc_code',
        'status',
        'oversize_charge',
        'handling_charge',
    ];

    protected $casts = [
        'shipper_same_as_customer' => 'boolean',
        'shipper_email_opt_out' => 'boolean',
        'consignee_email_opt_out' => 'boolean',
        'invoice_date' => 'date',
        'invoice_amount' => 'decimal:2',
        'oversize_charge' => 'decimal:2',
        'handling_charge' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns this shipment.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the shipper info for this shipment.
     */
    public function shipperInfo()
    {
        return $this->belongsTo(ShipperInfo::class, 'shipper_id');
    }
}