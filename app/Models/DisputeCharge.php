<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisputeCharge extends Model
{
    protected $table = 'dispute_surcharge_charges';

    protected $fillable = [
        'additional_charges',
        'conditions',
        'destination',
        'service_id',
        'calculation_type',
        'values',
        'gst_percentage',
        'place_of_apply',
        'status',
    ];

    protected $casts = [
        'gst_percentage' => 'decimal:2',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
