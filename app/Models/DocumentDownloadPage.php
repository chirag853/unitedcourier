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
     * Get the content attribute (backward-compatible accessor).
     * The page_meta column stores JSON page-level metadata.
     */
    public function getContentAttribute($value)
    {
        if (!empty($this->page_meta)) {
            $decoded = json_decode($this->page_meta, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
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