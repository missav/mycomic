<?php

namespace App\Models;

use App\Concerns\WithReviews;
use App\FileSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Chapter extends Model
{
    use WithReviews;

    public $incrementing = false;

    protected $casts = [
        'has_downloaded_pages' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
    }

    public function missingPages(): HasMany
    {
        return $this->hasMany(MissingPage::class);
    }

    public function title(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'cn' ? cn($this->title) : $this->title;
    }

    public function type(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'cn' ? cn($this->type) : zh($this->type);
    }

    public function sourceUrl(string $subdomain = 'tw'): string
    {
        return "https://{$subdomain}.manhuagui.com/comic/{$this->comic_id}/{$this->id}.html";
    }

    public function url(): string
    {
        return localizedRoute('chapters.view', ['chapter' => $this]);
    }

    public function pageImageDirectory(): string
    {
        return "/chapters/{$this->id}";
    }

    public function pageImagePath(int $page): string
    {
        return FileSignature::append("{$this->pageImageDirectory()}/{$page}.jpg");
    }

    public function pageCdnUrl(int $page): string
    {
        return cdn($this->pageImagePath($page));
    }

    public function pageOriginUrl(int $page): string
    {
        return origin($this->pageImagePath($page));
    }

    public function getPages(): Collection
    {
        $pageSizes = str($this->page_sizes)
            ->explode(',')
            ->map(function (string $sizes) {
                if (! $sizes) {
                    return ['width' => 0, 'height' => 0];
                }

                list($width, $height) = explode('x', $sizes);

                return ['width' => $width, 'height' => $height];
            })
            ->all();

        return collect(range(1, $this->pages))->map(fn (int $page, int $index) => array_merge([
            'number' => $page,
            'url' => $this->pageCdnUrl($page),
        ], $pageSizes[$index] ?? ['width' => 0, 'height' => 0]));
    }

    public function previous(): ?static
    {
        return static::query()
            ->where('comic_id', $this->comic_id)
            ->where('type', $this->type)
            ->where('id', '<', $this->id)
            ->orderByDesc('number')
            ->orderByDesc('id')
            ->first();
    }

    public function next(): ?static
    {
        return static::query()
            ->where('comic_id', $this->comic_id)
            ->where('type', $this->type)
            ->where('id', '>', $this->id)
            ->orderBy('number')
            ->orderBy('id')
            ->first();
    }

    public function lock(): void
    {
        $this->update(['locked_at' => now()]);
    }

    public function unlock(): void
    {
        $this->update(['locked_at' => null]);
    }
}
