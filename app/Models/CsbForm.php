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
        'ad_code',
        'iec_number',
        'bank_account_number',
        'lut_document',
    ];

    protected $casts = [
        'is_csb_v' => 'boolean',
        'is_gst' => 'boolean',
        'is_lut' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
