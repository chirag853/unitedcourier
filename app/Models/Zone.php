<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $table = 'zone';

    protected $primaryKey = 'zone_id';

    protected $fillable = [
        'zone_name',
        'zone_code',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the ups rates for this zone.
     */
    public function upsRates()
    {
        return $this->hasMany(UpsRate::class, 'zone_id', 'zone_id');
    }
}