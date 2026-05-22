<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicInfo extends Model
{
    use HasFactory;

    protected $table = 'basic_info';

    protected $fillable = [
        'first_name',
        'last_name',
        'job_title',
        'company_name',
        'email',
        'email_opt_out',
        'phone_1',
        'phone_2',
        'fax',
        'deals',
        'date_of_birth',
        'reviews',
        'owner',
        'tags',
        'source',
        'industry',
        'currency',
        'language',
        'description',
    ];

    protected $casts = [
        'email_opt_out' => 'boolean',
        'date_of_birth' => 'date',
        'reviews' => 'decimal:1',
        'created_at' => 'datetime',
    ];

    /**
     * Get the address info for this basic info.
     */
    public function addressInfo()
    {
        return $this->hasOne(AddressInfo::class, 'basic_info_id');
    }

    /**
     * Get the social profile for this basic info.
     */
    public function socialProfile()
    {
        return $this->hasOne(SocialProfile::class, 'basic_info_id');
    }

    /**
     * Get the access settings for this basic info.
     */
    public function access()
    {
        return $this->hasOne(Access::class, 'basic_info_id');
    }

    /**
     * Get the deals for this basic info.
     */
    public function deals()
    {
        return $this->hasMany(Deal::class, 'basic_info_id');
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
