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

    public function name(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'cn' ? cn($this->name) : $this->name;
    }

    public function url(): string
    {
        return localizedRoute('comics.index', ['filter' => ['tag' => $this->slug]]);
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
