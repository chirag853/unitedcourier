<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsbForm extends Model
{
    protected $fillable = [
        'customer_id',
        'aadhar_number',
        'aadhar_verified',
        'aadhar_document',
        'signature_document',
        'is_csb_v',
        'is_gst',
        'is_lut',
        'lut_number',
        'lut_verified',
        'lut_expiry_date',
        'lut_bond_year',
        'ad_code',
        'ad_code_document',
        'iec_number',
        'iec_document',
        'gst_certificate_number',
        'gst_certificate_document',
        'bank_account_number',
        'bank_type',
        'lut_document',
        'gst_document',
        'billing_address',
        'billing_gst',
        'billing_contact',
        'billing_email',
        'merchant_agreement',
        'merchant_agreement_accepted_at',
    ];

    protected $casts = [
        'is_csb_v' => 'boolean',
        'is_gst' => 'boolean',
        'is_lut' => 'boolean',
        'lut_verified' => 'boolean',
        'aadhar_verified' => 'boolean',
        'lut_expiry_date' => 'date',
        'merchant_agreement_accepted_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
