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
     */
    public function getDataAttribute($value)
    {
        // 1. Start with raw data column (what controller saves to)
        $data = [];
        if (is_array($value)) {
            // The 'data' => 'array' cast already decoded this from JSON
            $data = $value;
        } elseif (!empty($value)) {
            // Raw JSON string from DB (cast not involved)
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        // 2. Overlay normalized columns (seeder data) on top — these take priority
        if (!empty($this->data_title)) $data['title'] = $this->data_title;
        if (!empty($this->data_description)) $data['description'] = $this->data_description;
        if (!empty($this->data_icon)) $data['icon'] = $this->data_icon;
        if (!empty($this->data_image)) $data['image'] = $this->data_image;
        if (!empty($this->data_link)) $data['link'] = $this->data_link;
        if (!empty($this->data_button_text)) $data['button_text'] = $this->data_button_text;

        // 3. Merge any extra content stored as JSON
        if (!empty($this->data_extra)) {
            $extra = json_decode($this->data_extra, true);
            if (is_array($extra)) {
                $data = array_merge($data, $extra);
            }
        }

        return $data;
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
