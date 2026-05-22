<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAndConditionPage extends Model
{
    use HasFactory;

    protected $table = 'terms_and_condition_page';

    protected $fillable = [
        'section_key',
        'title',
        'paragraphs',
        'list_items_text',
        'sort_order',
        'effective_date',
        'footer_heading',
        'footer_email',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeBySection($query, $sectionKey)
    {
        return $query->where('section_key', $sectionKey);
    }

    /**
     * Get list items as array (backward compatibility)
     */
    public function getListItemsAttribute()
    {
        if ($this->list_items_text) {
            return explode("\n", $this->list_items_text);
        }
        return [];
    }
}
