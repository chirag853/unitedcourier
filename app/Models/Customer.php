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
        'pan_number',
        'pan_verified',
        'csb_status'
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_terms_accepted' => 'boolean',
        'email_verified' => 'boolean',
        'aadhar_verified' => 'boolean',
        'pan_verified' => 'boolean',
        'csb_status' => 'integer',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the customer's latest KYC detail record.
     */
    public function kycDetail()
    {
        return $this->hasOne(KycDetail::class)->latest();
    }

    /**
     * Get the customer's latest CSB form record.
     */
    public function csbForm()
    {
        return $this->hasOne(CsbForm::class)->latest();
    }
}
