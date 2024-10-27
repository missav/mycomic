<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $casts = [
        'has_bookmarked' => 'boolean',
    ];
}
