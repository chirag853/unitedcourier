<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageContent extends Model
{
    use HasFactory;

    protected $table = 'home_page';

    protected $fillable = [
        'section',
        'field_name',
        'content',
        'sort_order',
    ];

    protected $casts = [
        'content' => 'string',
        'sort_order' => 'integer',
    ];

    public $timestamps = true;
}
