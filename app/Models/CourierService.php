<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierService extends Model
{
    use HasFactory;

    protected $table = 'courier_services';

    public $timestamps = false;

    protected $fillable = [
        'shipper_code',
        'network',
        'service_code',
        'type',
        'method',
        'tat',
    ];

    /**
     * Get all rates that belong to this courier service.
     */
    public function rates()
    {
        return $this->hasMany(CourierRate::class, 'service_id');
    }
}