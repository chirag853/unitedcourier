<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'blog_title', 'url_title', 'slug', 'category_id',
        'sub_heading', 'sub_content',
        'seo_meta_title', 'image_alt', 'social_title',
        'country_name', 'state_name', 'city_name',
        'blog_description',
        'meta_description', 'meta_keyword',
        'og_title', 'og_url', 'og_description', 'og_image_url',
        'twitter_card',
        'master_image', 'master_image_alt_text',
        'is_trending', 'status',
        'author_name', 'author_description', 'author_image',
        'feed',
    ];

    protected $casts = [
        'is_trending' => 'string',
        'status' => 'string',
    ];

    /**
     * Get the category that this blog belongs to.
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Scope: only active blogs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope: order by created_at descending (newest first).
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope: only trending blogs.
     */
    public function scopeTrending($query)
    {
        return $query->where('is_trending', 'Yes');
    }
}