<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnersSectionCommonPage extends Model
{
    protected $table = 'partners_section_common_page';

    protected $fillable = [
        'logo_image',
        'alt_text',
        'display_order',
        'status',
    ];

    /**
     * Scope to only include active logos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to order by display_order ascending.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}