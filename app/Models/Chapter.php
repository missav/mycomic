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
        'locked_at' => 'timestamp',
    ];

    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
    }

    public function sourceUrl(): string
    {
        return "https://tw.manhuagui.com/comic/{$this->comic_id}/{$this->id}.html";
    }

    public function url(): string
    {
        return route('chapters.view', ['chapter' => $this]);
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

    public function unlock(array $data = []): void
    {
        $this->update(array_merge($data, ['locked_at' => null]));
    }

    public function isLocked(int $seconds = 1800): bool
    {
        return $this->locked_at && now()->subSeconds($seconds)->lessThanOrEqualTo($this->locked_at);
    }
}
