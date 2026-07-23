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

    // The home_page table only has an `updated_at` column (no `created_at`).
    // Disable created_at so inserts/updates don't try to write a non-existent column.
    public $timestamps = true;
    public const CREATED_AT = null;
    public const UPDATED_AT = 'updated_at';
}
