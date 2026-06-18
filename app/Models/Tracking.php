<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    protected $table = 'tracking';

    public const UPDATED_AT = null;

    protected $fillable = [
        'shipping_id',
        'uwc_id',
        'title',
        'status',
        'created_at',
    ];

    protected $casts = [
        'shipping_id' => 'unsignedBigInteger',
        'created_at' => 'datetime',
    ];

    /**
     * Get the shipment associated with this tracking record.
     */
    public function shipment()
    {
        return $this->belongsTo(CreateShipment::class, 'shipping_id');
    }
}
