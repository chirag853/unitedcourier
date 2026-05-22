<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsbInformation extends Model
{
    use HasFactory;

    protected $table = 'csb_information';

    protected $fillable = [
        'shipper_id',
        'ecommerce',
        'scheme',
        'bond_ut_igst',
        'lut_number',
        'iec_code',
        'gst_number',
        'ad_code',
        'bank_account_number',
        'bank_ifsc_code',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the shipments for this CSB information.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'csb_information_id');
    }

    /**
     * Get the ecommerce options.
     */
    public static function getEcommerceOptions()
    {
        return ['Yes', 'No'];
    }

    /**
     * Get the scheme options.
     */
    public static function getSchemeOptions()
    {
        return ['Yes', 'No'];
    }

    /**
     * Get the bond UT/IGST options.
     */
    public static function getBondUtIgstOptions()
    {
        return ['Bond UT', 'IGST'];
    }
}
