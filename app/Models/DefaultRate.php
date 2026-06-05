<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultRate extends Model
{
    use HasFactory;

    protected $table = 'default_rate';

    protected $fillable = [
        'network',
        'service',
        'type',
        'method',
        'expected_delivery',
        'weight_start_gm',
        'weight_end_gm',
        'zone_id',
        'rate',
    ];

    protected $casts = [
        'weight_start_gm' => 'decimal:3',
        'weight_end_gm' => 'decimal:3',
        'rate' => 'decimal:2',
    ];

    /**
     * Get the zone that this default rate belongs to.
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zone_id');
    }
}