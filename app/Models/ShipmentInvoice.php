<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentInvoice extends Model
{
    use HasFactory;

    protected $table = 'shipment_invoice';

    protected $fillable = [
        'shipper_id',
        'invoice_number',
        'invoice_date',
        'invoice_amount',
        'incoterms',
        'invoice_currency',
        'reference_number',
        'status',
        'delivery_type',
        'assigned_delivery_person',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'invoice_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the invoice items for this shipment invoice.
     */
    public function invoiceItems()
    {
        return $this->hasMany(ShipmentInvoiceItem::class, 'invoice_id');
    }

    /**
     * Get the shipments for this invoice.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'shipment_invoice_id');
    }

    /**
     * Get the total amount from invoice items.
     */
    public function getTotalAmountAttribute()
    {
        return $this->invoiceItems()->sum('amount');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($invoice) {
            // Delete related invoice items when invoice is deleted
            $invoice->invoiceItems()->delete();
        });
    }
}
