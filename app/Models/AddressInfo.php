<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressInfo extends Model
{
    use HasFactory;

    protected $table = 'address_info';

    protected $fillable = [
        'basic_info_id',
        'street_address',
        'country',
        'state_province',
        'city',
        'zipcode',
    ];

    protected $casts = [
        'basic_info_id' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the basic info for this address.
     */
    public function basicInfo()
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }

    /**
     * Get the full address attribute.
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->street_address,
            $this->city,
            $this->state_province,
            $this->country,
            $this->zipcode,
        ]);

        return implode(', ', $parts);
    }
}
