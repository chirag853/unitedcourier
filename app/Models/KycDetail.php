<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycDetail extends Model
{
    protected $fillable = [
        'customer_id',
        'gst_number',
        'gst_verified',
        'otp_verified',
        'aadhar_number',
        'aadhar_verified',
        'organization_name',
        'authorized_signatory',
        'terms_accepted',
        'terms_accepted_at',
        'kyc_status'
    ];

    protected $casts = [
        'gst_verified' => 'boolean',
        'otp_verified' => 'boolean',
        'aadhar_verified' => 'boolean',
        'terms_accepted' => 'boolean',
        'terms_accepted_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
