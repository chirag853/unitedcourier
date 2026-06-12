<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsigneeInfo extends Model
{
    use HasFactory;

    protected $table = 'consignee_info';

    protected $fillable = [
        'shipper_id',
        'delivery_destination',
        'origin_type',
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
        'email_opt_out',
    ];

    protected $casts = [
        'email_opt_out' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the shipments for this consignee.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'consignee_id');
    }
}
