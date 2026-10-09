<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipperConsignee extends Model
{
    use HasFactory;

    protected $table = 'shipper_consignee';

    protected $fillable = [
        'customer_id',
        'delivery_destination',
        'origin_type',
        'reason_for_export',
        'business_type',
        'consignee_name',
        'contact_person',
        'address_line1',
        'address_line2',
        'address_line3',
        'zip_code',
        'city',
        'state',
        'phone_number',
        'email',
    ];

    /**
     * The owning customer account.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
