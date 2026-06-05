<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Method extends Model
{
    use HasFactory;

    protected $table = 'methods';

    protected $fillable = [
        'method_type',
        'method_name',
        'method_value',
        'service_code',
        'service_description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to filter by method type (ddp or ddu).
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('method_type', $type);
    }

    /**
     * Scope to get only DDP methods.
     */
    public function scopeDdp($query)
    {
        return $query->where('method_type', 'ddp');
    }

    /**
     * Scope to get only DDU methods.
     */
    public function scopeDdu($query)
    {
        return $query->where('method_type', 'ddu');
    }

    /**
     * Scope to get only active methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get methods ordered by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}