<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkOffice extends Model
{
    use HasFactory;

    protected $table = 'network_page';

    protected $fillable = [
        'name',
        'type', // 'india' or 'overseas'
        'address',
        'telephone',
        'mobile',
        'fax',
        'email',
        'contact_person',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Scope to get only India offices
     */
    public function scopeIndia($query)
    {
        return $query->where('type', 'india');
    }

    /**
     * Scope to get only overseas offices
     */
    public function scopeOverseas($query)
    {
        return $query->where('type', 'overseas');
    }

    /**
     * Scope to get only active offices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
