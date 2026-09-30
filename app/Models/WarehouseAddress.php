<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseAddress extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'pin',
        'country',
        'registered_name',
        'return_address',
        'return_pin',
        'return_city',
        'return_state',
        'return_country',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
