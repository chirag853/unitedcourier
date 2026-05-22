<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipperInfo extends Model
{
    use HasFactory;

    protected $table = 'shipper_info';

    protected $fillable = [
        'delivery_destination',
        'origin_type',
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
}
