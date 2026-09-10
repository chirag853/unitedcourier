<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentRemark extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipment_remark';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'shipper_id',
        'entry_remark',
        'finance_remark',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'customer_id' => 'integer',
        'shipper_id'  => 'integer',
    ];

    /**
     * Get the customer that owns this remark.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the shipper that owns this remark.
     */
    public function shipper()
    {
        return $this->belongsTo(ShipperInfo::class, 'shipper_id');
    }
}
