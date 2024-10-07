<?php

namespace App\Models;

use App\FileSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model
{
    public $incrementing = false;

    protected $casts = [
        'has_downloaded_pages' => 'boolean',
    ];

    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
    }

    public function sourceUrl(): string
    {
        return "https://tw.manhuagui.com/comic/{$this->comic_id}/{$this->id}.html";
    }

    public function pageImagePath(int $page): string
    {
        return FileSignature::append("/chapters/{$this->id}/{$page}.jpg");
    }
}
