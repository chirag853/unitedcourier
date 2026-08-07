<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycDraft extends Model
{
    protected $fillable = [
        'customer_id',
        'kyc_type',
        'current_step',
        'form_data',
    ];

    protected $casts = [
        'current_step' => 'integer',
        'form_data' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
