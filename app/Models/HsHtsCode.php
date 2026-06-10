<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsHtsCode extends Model
{
    protected $table = 'hs_hts_codes';

    protected $fillable = [
        'items',
        'hs_code',
        'hts_code',
    ];
}