<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }
}
