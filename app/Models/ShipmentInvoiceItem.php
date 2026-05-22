<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'shipment_invoice_items';

    protected $fillable = [
        'invoice_id',
        'box_no',
        'description',
        'hs_code',
        'unit_type',
        'qty',
        'unit_rate',
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'box_no' => 'integer',
        'qty' => 'decimal:2',
        'unit_rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the shipment invoice for this item.
     */
    public function shipmentInvoice()
    {
        return $this->belongsTo(ShipmentInvoice::class, 'invoice_id');
    }

    /**
     * Get the unit type options.
     */
    public static function getUnitTypeOptions()
    {
        return [
            'PCS',
            'KG',
            'NOS',
            'Bottle',
            'Pair',
            'Strip',
            'Dozen',
            'Gross',
            'Sets',
            'Box',
            'Container',
            'Carats',
            'Pairs',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Note: amount is a generated column in database, calculated automatically
    }
}
