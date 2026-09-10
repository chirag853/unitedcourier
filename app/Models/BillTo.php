<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillTo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bill_to';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipper_id',
        'customer_id',
        'biller_name',
    ];

    /**
     * Get the shipper that owns this bill-to record.
     */
    public function shipper()
    {
        return $this->belongsTo(ShipperInfo::class, 'shipper_id');
    }

    /**
     * Get the customer linked to this bill-to record.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
