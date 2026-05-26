<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRateCalculatorPage extends Model
{
    use HasFactory;

    protected $table = 'shipping_rate_calculator_page';

    protected $fillable = [
        'section_type',
        'title',
        'subtitle',
        'description',
        'image',
        'icon_svg',
        'link',
        'display_order',
        'page_badge_text',
        'page_button_text',
        'page_tag',
        'page_label',
        'page_placeholder',
        'page_icon_class',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope to get items by section type
     */
    public function scopeBySection($query, $sectionType)
    {
        return $query->where('section_type', $sectionType);
    }

    /**
     * Scope to get active items ordered by display_order
     */
    public function scopeActiveOrdered($query)
    {
        return $query->where('status', true)->orderBy('display_order');
    }
}