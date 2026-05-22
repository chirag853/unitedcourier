<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    protected $table = 'access';

    protected $fillable = [
        'basic_info_id',
        'visibility',
    ];

    protected $casts = [
        'basic_info_id' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the basic info for this access setting.
     */
    public function basicInfo()
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }

    /**
     * Get the visibility options.
     */
    public static function getVisibilityOptions()
    {
        return [
            'public' => 'Public',
            'private' => 'Private',
            'select_people' => 'Select People',
        ];
    }

    /**
     * Check if the visibility is public.
     */
    public function isPublic()
    {
        return $this->visibility === 'public';
    }

    /**
     * Check if the visibility is private.
     */
    public function isPrivate()
    {
        return $this->visibility === 'private';
    }

    /**
     * Check if the visibility is select people.
     */
    public function isSelectPeople()
    {
        return $this->visibility === 'select_people';
    }
}
