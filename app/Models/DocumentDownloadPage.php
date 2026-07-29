<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDownloadPage extends Model
{
    protected $table = 'document_download_page';

    protected $fillable = [
        'file_type',
        'title',
        'file_size',
        'file_url',
        'category',
        'status_badge',
        'badge_text',
        'hero_image',
        'description',
        'sort_order',
        'status',
        'section',
        'page_meta',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Build the legacy content array from normalized hero columns.
     */
    public function getContentAttribute($value)
    {
        return [
            'badge' => $this->badge_text,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->hero_image,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }
}