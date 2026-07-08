<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsbForm extends Model
{
    protected $fillable = [
        'customer_id',
        'is_csb_v',
        'is_gst',
        'is_lut',
        'lut_verified',
        'ad_code',
        'iec_number',
        'iec_document',
        'gst_certificate_number',
        'gst_certificate_document',
        'bank_account_number',
        'bank_type',
        'lut_document',
        'gst_document',
    ];

    protected $casts = [
        'is_csb_v' => 'boolean',
        'is_gst' => 'boolean',
        'is_lut' => 'boolean',
        'lut_verified' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
