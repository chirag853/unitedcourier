<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDimension extends Model
{
    use HasFactory;

    protected $table = 'package_dimension';

    protected $fillable = [
        'shipper_id',
        'shipping_method',
        'actual_weight_kg',
        'length_cm',
        'width_cm',
        'height_cm',
        'volumetric_weight',
        'chargeable_weight',
    ];

    protected $casts = [
        'actual_weight_kg' => 'decimal:2',
        'length_cm' => 'decimal:2',
        'width_cm' => 'decimal:2',
        'height_cm' => 'decimal:2',
        'volumetric_weight' => 'decimal:2',
        'chargeable_weight' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the shipments for this package dimension.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'package_dimension_id');
    }

    /**
     * Get the invoice items for this package dimension.
     */
    public function invoiceItems()
    {
        return $this->hasMany(ShipmentInvoiceItem::class, 'package_dimension_id');
    }

    /**
     * Calculate volumetric weight based on dimensions.
     */
    public function calculateVolumetricWeight()
    {
        if ($this->length_cm && $this->width_cm && $this->height_cm) {
            return ($this->length_cm * $this->width_cm * $this->height_cm) / 5000;
        }
        return null;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($package) {
            // Auto-calculate volumetric weight if dimensions are provided
            if ($package->length_cm && $package->width_cm && $package->height_cm) {
                $package->volumetric_weight = $package->calculateVolumetricWeight();
            }
            // Auto-calculate chargeable weight (max of actual and volumetric)
            $actual = floatval($package->actual_weight_kg) ?: 0;
            $volumetric = floatval($package->volumetric_weight) ?: 0;
            $package->chargeable_weight = max($actual, $volumetric);
        });
    }
}
