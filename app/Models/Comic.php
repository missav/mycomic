<?php

namespace App\Models;

use App\Enums\ComicAudience;
use App\Enums\ComicCountry;
use App\FileSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comic extends Model
{
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'country' => ComicCountry::class,
            'audience' => ComicAudience::class,
            'has_downloaded_cover' => 'boolean',
            'is_ended' => 'boolean',
            'last_updated_on' => 'date',
        ];
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    public function recentChapter(): HasOne
    {
        return $this->hasOne(Chapter::class)->latestOfMany();
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function name(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'cn' ? cn($this->name) : $this->name;
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

    public function shareUrl(string $channel): string
    {
        return match($channel) {
            'whatsapp' => 'https://wa.me/?' . http_build_query(['text' => "{$this->name()}\n\n{$this->url()}"]),
            'telegram' => 'https://t.me/share/url?' . http_build_query(['url' => $this->url(), 'text' => $this->name()]),
            'twitter' => 'https://twitter.com/intent/tweet?' . http_build_query(['text' => "{$this->name()}\n\n{$this->url()}"]),
            'email' => 'mailto:?' . http_build_query(['subject' => $this->name(), 'body' => "{$this->name()}\n\n{$this->url()}"]),
        };
    }

    public function toRecommendableArray(): array
    {
        return [
            'name' => $this->name('zh'),
            'name_cn' => $this->name('cn'),
            'author_ids' => $this->authors->pluck('id')->all(),
            'author_texts' => $this->authors->map->name('zh')->all(),
            'author_texts_cn' => $this->authors->map->name('cn')->all(),
            'tag_ids' => $this->tags->pluck('id')->all(),
            'tag_texts' => $this->tags->map->name('zh')->all(),
            'tag_texts_cn' => $this->tags->map->name('cn')->all(),
            'country' => $this->country->value,
            'country_text' => $this->country->text('zh'),
            'country_text_cn' => $this->country->text('cn'),
            'audience' => $this->audience->value,
            'audience_text' => $this->audience->text('zh'),
            'audience_text_cn' => $this->audience->text('cn'),
            'year' => $this->year,
            'is_ended' => $this->is_ended,
            'cover_image_path' => $this->coverImagePath(),
            'last_updated_on' => $this->last_updated_on,
        ];
    }

    public static function sourceUrl(int $id, string $subdomain = 'tw'): string
    {
        return "https://{$subdomain}.manhuagui.com/comic/{$id}/";
    }
}
