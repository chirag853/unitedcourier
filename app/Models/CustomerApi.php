<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerApi extends Model
{
    use HasFactory;

    protected $table = 'customer_api';

    protected $fillable = [
        'customer_id',
        'service_id',
        'status',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'service_id' => 'integer',
        'status' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function service()
    {
        return $this->belongsTo(CourierService::class, 'service_id');
    }

    public function isAllowed(): bool
    {
        return (int) $this->status === 1;
    }
}
