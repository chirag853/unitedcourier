<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactNumberSectionCommonPage extends Model
{
    use HasFactory;

    protected $table = 'fact_number_section_common_page';

    protected $fillable = [
        'title',
        'target_number',
        'suffix',
        'display_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope to get only active records.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to get records ordered by display_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}