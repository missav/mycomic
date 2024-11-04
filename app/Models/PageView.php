<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    use MassPrunable;

    public $timestamps = false;

    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(30)->toDateString());
    }

    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
    }
}
