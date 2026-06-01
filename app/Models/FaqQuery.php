<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqQuery extends Model
{
    use HasFactory;

    protected $table = 'faq_quries';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'message',
        'page_name',
    ];
}