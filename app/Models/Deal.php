<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    protected $table = 'deal';

    protected $fillable = [
        'deal_name',
        'pipeline',
        'status',
        'deal_value',
        'currency',
        'period',
        'period_value',
        'contacts',
        'project',
        'due_date',
        'expected_closing_date',
        'follow_up_date',
        'source',
        'tags',
        'priority',
        'description',
    ];

    protected $casts = [
        'deal_value' => 'decimal:2',
        'period_value' => 'integer',
        'due_date' => 'date',
        'expected_closing_date' => 'date',
        'follow_up_date' => 'date',
        'contacts' => 'array', // Store as array, will be converted to JSON
        'tags' => 'array', // Store as array, will be converted to JSON
        'created_at' => 'datetime',
    ];

    /**
     * Get the basic info for this deal.
     */
    public function basicInfo()
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }

    /**
     * Get the status options.
     */
    public static function getStatusOptions()
    {
        return [
            'Open' => 'Open',
            'Lost' => 'Lost',
            'Won' => 'Won',
        ];
    }

    /**
     * Get the priority options.
     */
    public static function getPriorityOptions()
    {
        return [
            'High' => 'High',
            'Medium' => 'Medium',
            'Low' => 'Low',
        ];
    }

    /**
     * Get the period options.
     */
    public static function getPeriodOptions()
    {
        return [
            'Days' => 'Days',
            'Month' => 'Month',
        ];
    }

    /**
     * Check if the deal is open.
     */
    public function isOpen()
    {
        return $this->status === 'Open';
    }

    /**
     * Check if the deal is lost.
     */
    public function isLost()
    {
        return $this->status === 'Lost';
    }

    /**
     * Check if the deal is won.
     */
    public function isWon()
    {
        return $this->status === 'Won';
    }

    /**
     * Get formatted deal value.
     */
    public function getFormattedDealValueAttribute()
    {
        return number_format($this->deal_value, 2) . ' ' . $this->currency;
    }

    /**
     * Scope to get only open deals.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    /**
     * Scope to get only won deals.
     */
    public function scopeWon($query)
    {
        return $query->where('status', 'Won');
    }

    /**
     * Scope to get only lost deals.
     */
    public function scopeLost($query)
    {
        return $query->where('status', 'Lost');
    }

    /**
     * Scope to get high priority deals.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'High');
    }

    /**
     * Get the days until due date.
     */
    public function getDaysUntilDueAttribute()
    {
        if ($this->due_date) {
            return now()->diffInDays($this->due_date, false);
        }
        return null;
    }

    /**
     * Get the days until expected closing date.
     */
    public function getDaysUntilClosingAttribute()
    {
        if ($this->expected_closing_date) {
            return now()->diffInDays($this->expected_closing_date, false);
        }
        return null;
    }
}
