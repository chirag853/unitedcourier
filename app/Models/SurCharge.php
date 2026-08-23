<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurCharge extends Model
{
    protected $table = 'sur_charges';

    protected $fillable = [
        'name',
        'code',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}