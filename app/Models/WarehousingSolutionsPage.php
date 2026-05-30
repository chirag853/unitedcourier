<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousingSolutionsPage extends Model
{
    use HasFactory;

    protected $table = 'warehousing_solutions_page';

    protected $fillable = [
        'section',
        'item_key',
        'content',
        'sort_order',
        'is_active',
        // Normalized columns from content JSON
        'icon_svg', 'icon_class', 'color_scheme',
        'badge_text', 'button_text', 'button_url', 'btn_text',
        'subtitle', 'paragraphs',
        'question', 'answer', 'name', 'avatar_url', 'rating', 'text_content',
        'stat_value', 'stat_label', 'stat_suffix',
        'logo_url', 'alt_text',
        'list_items_text', 'check_list_text', 'extra_content',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'decimal:1',
        'content' => 'array',
    ];

    /**
     * Get the content as an array (backward-compatible accessor)
     */
    public function getContentAttribute($value)
    {
        // Start with normalized column values
        $data = [
            'title'       => $this->title,
            'description' => $this->description,
            'image'       => $this->image,
            'link'        => $this->link,
            'icon_svg'    => $this->icon_svg,
            'icon_class'  => $this->icon_class,
            'color_class' => $this->color_scheme,
            'badge_text'  => $this->badge_text,
            'button_text' => $this->button_text,
            'button_url'  => $this->button_url,
            'btn_text'    => $this->btn_text,
            'subtitle'    => $this->subtitle,
            'paragraphs'  => $this->paragraphs,
            'question'    => $this->question,
            'answer'      => $this->answer,
            'name'        => $this->name,
            'avatar'      => $this->avatar_url,
            'rating'      => $this->rating,
            'text'        => $this->text_content,
            'value'       => $this->stat_value,
            'label'       => $this->stat_label,
            'suffix'      => $this->stat_suffix,
            'logo_url'    => $this->logo_url,
            'alt'         => $this->alt_text,
        ];

        // Bridge: some normalized column keys differ from what consumers expect
        if ($this->icon_svg !== null) {
            $data['icon'] = $this->icon_svg;
        }

        // Parse the JSON content column and merge its values on top of defaults.
        // $value is the raw string from the content column.
        // The getContentAttribute() accessor takes precedence over the
        // $casts = ['content' => 'array'] cast, so $value is the raw DB string.
        // JSON in the DB has embedded control characters (literal \n in HTML content)
        // that cause json_decode to fail. Sanitize by stripping control chars.
        $originalContent = null;
        if (is_string($value)) {
            $originalContent = json_decode($value, true);
            if (!is_array($originalContent)) {
                // Strip control characters (0x00-0x1F except 0x09 tab, 0x0A LF, 0x0D CR)
                // but keep CR/LF as they may appear inside JSON string values
                // Actually, just replace literal LF with escaped \n
                $cleaned = str_replace(["\r\n", "\n", "\r"], ["\\r\\n", "\\n", "\\r"], $value);
                $originalContent = json_decode($cleaned, true);
            }
        } elseif (is_array($value)) {
            $originalContent = $value;
        }

        if (is_array($originalContent)) {
            foreach ($originalContent as $key => $val) {
                if (!array_key_exists($key, $data) || $data[$key] === null) {
                    $data[$key] = $val;
                }
            }
        }

        if (!empty($this->list_items_text)) {
            $data['list_items'] = explode("\n", $this->list_items_text);
        }
        if (!empty($this->check_list_text)) {
            $data['check_list'] = explode("\n", $this->check_list_text);
        }

        // Merge any extra content stored as JSON string
        // Only fill in null/missing keys so content column takes priority
        $extraContent = $this->extra_content;
        if (is_string($extraContent)) {
            $extraContent = json_decode($extraContent, true);
        }
        if (is_array($extraContent)) {
            foreach ($extraContent as $key => $val) {
                if (!array_key_exists($key, $data) || $data[$key] === null) {
                    $data[$key] = $val;
                }
            }
        }

        // Bridge: ensure list_items key consistency (list_items_text column → list_items)
        if (!empty($this->list_items_text) && !isset($data['list_items'])) {
            $data['list_items'] = explode("\n", $this->list_items_text);
        }

        return $data;
    }

    /**
     * Scope to get active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
}
