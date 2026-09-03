<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExporterCustomer extends Model
{
    protected $fillable = [
        'exporter_id',
        'business_category_id',
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
        'aadhar_front_document',
        'aadhar_back_document',
        'pan_number',
        'pan_holder_name',
        'pan_dob',
        'pan_document',
        'csb_type',
        'is_lut',
        'is_gst',
        'gst_certificate_number',
        'gst_business_name',
        'gst_certificate_document',
        'ad_code',
        'ad_code_document',
        'iec_number',
        'iec_document',
        'bank_account_number',
        'bank_type',
        'lut_bond_year',
        'lut_expiry_date',
        'lut_document',
        'billing_address',
        'billing_contact',
        'billing_email',
        'merchant_agreement',
        'terms_accepted',
        'merchant_agreement_accepted_at',
    ];

    protected $casts = [
        'email_opt_out' => 'boolean',
        'is_lut' => 'boolean',
        'is_gst' => 'boolean',
        'terms_accepted' => 'boolean',
        'lut_expiry_date' => 'date',
        'pan_dob' => 'date',
        'merchant_agreement_accepted_at' => 'datetime',
    ];

    public function exporter()
    {
        return $this->belongsTo(Customer::class, 'exporter_id');
    }

    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    /**
     * Extra addresses saved for this saved customer. The main address is still
     * stored directly on the exporter_customers row (primary), while every
     * additional "Save Address" click appends a row here.
     */
    public function addresses()
    {
        return $this->hasMany(ExporterCustomerAddress::class, 'exporter_customer_id')
            ->orderBy('id');
    }

    /**
     * All addresses to display for this saved customer: the primary (flat)
     * address first, followed by every extra address that was appended.
     *
     * @return array<int, array<string, mixed>>
     */
    public function displayAddresses(): array
    {
        $items = [];

        if (! empty($this->address_line1)) {
            $items[] = [
                'address_line1' => (string) $this->address_line1,
                'address_line2' => $this->address_line2,
                'address_line3' => $this->address_line3,
                'pincode' => (string) $this->pincode,
                'city' => (string) $this->city,
                'state' => (string) $this->state,
                'is_primary' => true,
            ];
        }

        foreach ($this->addresses as $child) {
            $items[] = [
                'address_line1' => (string) $child->address_line1,
                'address_line2' => $child->address_line2,
                'address_line3' => $child->address_line3,
                'pincode' => (string) $child->pincode,
                'city' => (string) $child->city,
                'state' => (string) $child->state,
                'is_primary' => false,
            ];
        }

        return $items;
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
