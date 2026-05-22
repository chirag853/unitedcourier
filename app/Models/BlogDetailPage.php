<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogDetailPage extends Model
{
    protected $table = 'blog_detail_page';

    protected $fillable = [
        'blog_id', 'section_key', 'section_title',
        'section_content', 'section_type', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the blog that owns this detail section.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Scope a query to only include active sections.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Scope a query to filter by section type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('section_type', $type);
    }
}