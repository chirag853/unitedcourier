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
        'awb_number',
        'shipper_id',
        'shipping_id',
        'uwc_id',
        'title',
        'status',
        'created_at',
    ];

    protected $casts = [
        'shipper_id' => 'integer',
        'shipping_id' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the shipment associated with this tracking record.
     */
    public function shipment()
    {
        return $this->belongsTo(CreateShipment::class, 'shipping_id');
    }

    /**
     * Get the shipper info associated with this tracking record.
     */
    public function shipper()
    {
        return $this->belongsTo(ShipperInfo::class, 'shipper_id');
    }

    /**
     * Status title mapping for display purposes.
     */
    public static function getStatusTitleMap(): array
    {
        return [
            'draft'              => 'Order Created',
            'ready'              => 'Payment Confirmed',
            'assigned_for_pickup'=> 'Assigned for Pickup',
            'packed'             => 'Shipment Packed',
            'manifested'         => 'Shipment Manifested',
            'dispatched'         => 'Shipment Dispatched',
            'delivered'          => 'Shipment Delivered',
            'cancelled'          => 'Shipment Cancelled',
            'disputed'           => 'Shipment Disputed',
            'on_hold'            => 'Shipment On Hold',
            'received'           => 'Shipment Received',
        ];
    }

    /**
     * Get the display title for a status.
     */
    public static function getTitleForStatus(string $status): string
    {
        return self::getStatusTitleMap()[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }
}
