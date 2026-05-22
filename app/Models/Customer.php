<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name', 
        'email',
        'phone_number',
        'alternate_phone_number',
        'password_hash',
        'aadhar_number',
        'business_category_id',
        'is_terms_accepted',
        'email_verified',
        'aadhar_verified',
        'csb_status'
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_terms_accepted' => 'boolean',
        'email_verified' => 'boolean',
        'aadhar_verified' => 'boolean',
        'csb_status' => 'integer',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
