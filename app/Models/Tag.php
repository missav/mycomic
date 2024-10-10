<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    public $timestamps = false;

    public function comics(): BelongsToMany
    {
        return $this->belongsToMany(Comic::class);
    }

    public function url(): string
    {
        return route('comics.index', ['tag' => $this->name]);
    }
}
