<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutPageContent extends Model
{
    use HasFactory;

    protected $table = 'about_page_content';

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
        'page_target_number',
        'page_suffix',
        'page_button_text',
        'page_tag',
        'page_color_scheme',
        'page_year',
        'page_card_color_class',
        'page_rating',
        'page_countries',
        'page_pin_codes',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'page_rating' => 'decimal:1',
    ];

    /**
     * Scope to get items by section type
     */
    public function scopeBySection($query, $sectionType)
    {
        return $query->where('section_type', $sectionType);
    }

    /**
     * Get formatted extra data as an array (backward compatibility)
     */
    public function getFormattedExtraData()
    {
        $data = [];
        if ($this->page_badge_text) $data['badge_text'] = $this->page_badge_text;
        if ($this->page_target_number) $data['target_number'] = $this->page_target_number;
        if ($this->page_suffix) $data['suffix'] = $this->page_suffix;
        if ($this->page_button_text) $data['button_text'] = $this->page_button_text;
        if ($this->page_tag) $data['tag'] = $this->page_tag;
        if ($this->page_color_scheme) $data['color_scheme'] = $this->page_color_scheme;
        if ($this->page_year) $data['year'] = $this->page_year;
        if ($this->page_card_color_class) $data['card_color_class'] = $this->page_card_color_class;
        if ($this->page_rating) $data['rating'] = $this->page_rating;
        if ($this->page_countries) {
            $data['countries'] = json_decode($this->page_countries, true) ?? $this->page_countries;
        }
        if ($this->page_pin_codes) {
            $data['pin_codes'] = json_decode($this->page_pin_codes, true) ?? $this->page_pin_codes;
        }
        return $data;
    }

    /**
     * Accessor for backward compatibility - extra_data returns array
     */
    public function getExtraDataAttribute()
    {
        return $this->getFormattedExtraData();
    }
}
