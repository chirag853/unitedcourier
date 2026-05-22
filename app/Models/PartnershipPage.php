<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnershipPage extends Model
{
    protected $table = 'partnership_page';

    protected $fillable = [
        'section', 'item_key', 'title', 'description', 'image', 'link', 'content', 'sort_order', 'status',
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
        'status' => 'string',
        'rating' => 'decimal:1',
    ];

    public function getContentAttribute($value)
    {
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

        if (!empty($this->list_items_text)) {
            $data['list_items'] = explode("\n", $this->list_items_text);
        }
        if (!empty($this->check_list_text)) {
            $data['check_list'] = explode("\n", $this->check_list_text);
        }

        // Merge any extra content stored as JSON
        if (!empty($this->extra_content)) {
            $extra = json_decode($this->extra_content, true);
            if (is_array($extra)) {
                $data = array_merge($data, $extra);
            }
        }

        return $data;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }
}