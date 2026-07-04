<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $table = 'zone';

    protected $primaryKey = 'id';

    protected $fillable = [
        'zone_id',
        'zone_name',
        'zone_code',
        'zone_number_testing',
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
        return $this->hasMany(UpsRate::class, 'zone_id', 'id');
    }
}