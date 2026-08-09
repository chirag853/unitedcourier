<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycDetail extends Model
{
    protected $fillable = [
        'customer_id',
        'kyc_type',
        'gst_number',
        'gst_certificate_document',
        'gst_verified',
        'otp_verified',
        'aadhar_number',
        'aadhar_verified',
        'pan_number',
        'pan_holder_name',
        'pan_dob',
        'pan_document',
        'pan_verified',
        'aadhar_front_document',
        'aadhar_back_document',
        'aadhar_address',
        'organization_name',
        'authorized_signatory',
        'signature',
        'signature_document',
        'merchant_agreement',
        'merchant_agreement_accepted_at',
        'billing_address',
        'billing_gst',
        'billing_contact',
        'billing_email',
        'terms_accepted',
        'terms_accepted_at',
        'kyc_status'
    ];

    protected $casts = [
        'gst_verified' => 'boolean',
        'otp_verified' => 'boolean',
        'aadhar_verified' => 'boolean',
        'pan_verified' => 'boolean',
        'pan_dob' => 'date',
        'terms_accepted' => 'boolean',
        'terms_accepted_at' => 'datetime',
        'merchant_agreement_accepted_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
