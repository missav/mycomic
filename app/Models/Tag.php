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
        return localizedRoute('comics.index', ['tag' => $this->name]);
    }

    public static function cached(): array
    {
        return cache()->remember('tags', 3600, function () {
            return Tag::get()
                ->mapWithKeys(fn (Tag $tag) => [
                    $tag->slug => $tag->name,
                ])
                ->all();
        });
    }
}
