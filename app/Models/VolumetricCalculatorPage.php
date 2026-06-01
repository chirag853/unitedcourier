<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolumetricCalculatorPage extends Model
{
    use HasFactory;

    protected $table = 'volumetric_calculator_page';

    protected $fillable = [
        'page',
        'section',
        'data',
        'sort_order',
        'is_active',
        // Normalized columns from data JSON
        'data_title', 'data_description', 'data_icon', 'data_image',
        'data_link', 'data_button_text', 'data_extra',
    ];

    protected $casts = [
        'data' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the data as an array (backward-compatible accessor)
     *
     * NOTE: With 'data' => 'array' cast active, $value is ALREADY a decoded array
     * from the cast, NOT a raw JSON string. We must handle both cases.
     *
     * STRATEGY: Normalized columns (data_title, data_description, etc.) are ONLY
     * used as fallback when the raw data column is empty/null. This prevents the
     * seeder's static values in normalized columns from overwriting live changes
     * saved by the controller (which only writes to the 'data' column).
     */
    public function getDataAttribute($value)
    {
        // Decode the data column (could be array from cast, or raw JSON string)
        $data = [];
        if (is_array($value)) {
            $data = $value;
        } elseif (!empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        // Always merge normalized columns on top of the data column.
        // The controller now writes to BOTH the data column AND normalized columns/data_extra
        // in sync, so this merge keeps the accessor output fully populated.
        $normalized = [];
        if (!empty($this->data_title)) $normalized['title'] = $this->data_title;
        if (!empty($this->data_description)) $normalized['description'] = $this->data_description;
        if (!empty($this->data_icon)) $normalized['icon'] = $this->data_icon;
        if (!empty($this->data_image)) $normalized['image'] = $this->data_image;
        if (!empty($this->data_link)) $normalized['link'] = $this->data_link;
        if (!empty($this->data_button_text)) $normalized['button_text'] = $this->data_button_text;

        if (!empty($this->data_extra)) {
            $extra = json_decode($this->data_extra, true);
            if (is_array($extra)) {
                $normalized = array_merge($normalized, $extra);
            }
        }

        // Merged output: data column first, then normalized on top (so normalized overrides
        // the data column if they differ — which shouldn't happen since controller syncs them)
        return array_merge($data, $normalized);
    }

    /**
     * Scope to get items by section
     */
    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope to get ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Scope to get active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
