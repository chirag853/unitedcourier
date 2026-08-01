<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExporterCustomer extends Model
{
    protected $fillable = [
        'exporter_id',
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
        'email_opt_out' => 'boolean',
    ];

    public function exporter()
    {
        return $this->belongsTo(Customer::class, 'exporter_id');
    }

    public function toShipperArray(): array
    {
        return [
            'shipper_company_names' => $this->company_name,
            'shipper_contact_person' => $this->contact_person,
            'shipper_address_line1' => $this->address_line1,
            'shipper_address_line2' => $this->address_line2,
            'shipper_address_line3' => $this->address_line3,
            'shipper_pincode' => $this->pincode,
            'shipper_city' => $this->city,
            'shipper_state' => $this->state,
            'shipper_phone_number' => $this->phone_number,
            'shipper_emails' => $this->email,
            'shipper_email_opt_out' => $this->email_opt_out,
            'shipper_kyc_type' => $this->kyc_type,
            'shipper_kyc_number' => $this->kyc_number,
        ];
    }
}
