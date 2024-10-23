<?php

namespace App\Models;

use App\Enums\ComicAudience;
use App\Enums\ComicCountry;
use App\FileSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comic extends Model
{
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'country' => ComicCountry::class,
            'audience' => ComicAudience::class,
            'has_downloaded_cover' => 'boolean',
            'is_ended' => 'boolean'
        ];
    }

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
        return localizedRoute('comics.view', ['comic' => $this]);
    }

    public function audienceUrl(): string
    {
        return localizedRoute('comics.index', ['audience' => $this->audience]);
    }

    public function countryUrl(): string
    {
        return localizedRoute('comics.index', ['country' => $this->country]);
    }

    public function coverImagePath(): string
    {
        return FileSignature::append("/comics/{$this->id}.jpg");
    }

    public function coverCdnUrl(): string
    {
        return cdn($this->coverImagePath());
    }

    public static function sourceUrl(int $id, string $subdomain = 'tw'): string
    {
        return "https://{$subdomain}.manhuagui.com/comic/{$id}/";
    }
}
