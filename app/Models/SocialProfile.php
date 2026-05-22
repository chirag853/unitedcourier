<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialProfile extends Model
{
    use HasFactory;

    protected $table = 'social_profile';

    protected $fillable = [
        'basic_info_id',
        'facebook',
        'skype',
        'linkedin',
        'twitter',
        'whatsapp',
        'instagram',
    ];

    protected $casts = [
        'basic_info_id' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the basic info for this social profile.
     */
    public function basicInfo()
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }

    /**
     * Get formatted phone number for WhatsApp.
     */
    public function getFormattedWhatsappAttribute()
    {
        if ($this->whatsapp) {
            // Remove any non-digit characters except + at the beginning
            return preg_replace('/[^0-9+]/', '', $this->whatsapp);
        }
        return null;
    }

    /**
     * Get Facebook URL.
     */
    public function getFacebookUrlAttribute()
    {
        if ($this->facebook) {
            return strpos($this->facebook, 'http') === 0 ? $this->facebook : 'https://facebook.com/' . $this->facebook;
        }
        return null;
    }

    /**
     * Get LinkedIn URL.
     */
    public function getLinkedinUrlAttribute()
    {
        if ($this->linkedin) {
            return strpos($this->linkedin, 'http') === 0 ? $this->linkedin : 'https://linkedin.com/in/' . $this->linkedin;
        }
        return null;
    }

    /**
     * Get Twitter URL.
     */
    public function getTwitterUrlAttribute()
    {
        if ($this->twitter) {
            return strpos($this->twitter, 'http') === 0 ? $this->twitter : 'https://twitter.com/' . $this->twitter;
        }
        return null;
    }

    /**
     * Get Instagram URL.
     */
    public function getInstagramUrlAttribute()
    {
        if ($this->instagram) {
            return strpos($this->instagram, 'http') === 0 ? $this->instagram : 'https://instagram.com/' . $this->instagram;
        }
        return null;
    }
}
