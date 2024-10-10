<?php

namespace App\Models;

use App\FileSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comic extends Model
{
    public $incrementing = false;

    protected $casts = [
        'has_downloaded_cover' => 'boolean',
        'is_finished' => 'boolean',
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function url(): string
    {
        return route('comics.view', ['comic' => $this]);
    }

    public function audienceUrl(): string
    {
        return route('comics.index', ['audience' => $this->audience]);
    }

    public function countryUrl(): string
    {
        return route('comics.index', ['country' => $this->country]);
    }

    public function coverImagePath(): string
    {
        return FileSignature::append("/comics/{$this->id}.jpg");
    }

    public function coverCdnUrl(): string
    {
        return cdn($this->coverImagePath());
    }

    public static function sourceUrl(int $id): string
    {
        return "https://tw.manhuagui.com/comic/{$id}/";
    }
}
