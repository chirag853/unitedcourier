<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessCategory extends Model
{
    protected $fillable = [
        'category_name',
        'category_slug',
        'description',
        'status',
        'display_order'
    ];

    protected $casts = [
        'status' => 'string',
        'display_order' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('category_name', 'asc');
    }
}
