<?php

namespace App\Models;

use App\FileSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    public function comics(): BelongsToMany
    {
        return $this->belongsToMany(Comic::class);
    }

    public function coverImagePath(): string
    {
        return FileSignature::append("/authors/{$this->id}.jpg");
    }

    public static function sourceUrl(int $id): string
    {
        return "https://tw.manhuagui.com/author/{$id}/";
    }
}
