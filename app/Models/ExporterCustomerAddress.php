<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExporterCustomerAddress extends Model
{
    protected $fillable = [
        'exporter_customer_id',
        'address_line1',
        'address_line2',
        'address_line3',
        'pincode',
        'city',
        'state',
    ];

    public function exporterCustomer()
    {
        return $this->belongsTo(ExporterCustomer::class, 'exporter_customer_id');
    }
}
