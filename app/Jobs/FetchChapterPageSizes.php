<?php

namespace App\Jobs;

use App\Models\Chapter;
use ErrorException;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class FetchChapterPageSizes implements ShouldQueue, ShouldBeUnique
{
    use Queueable, Dispatchable;

    public int $uniqueFor = 3600;

    public function __construct(
        protected Chapter $chapter,
    ) {
        $this->onQueue('heavy');
    }

    public function uniqueId(): string
    {
        return $this->chapter->id;
    }

    public function handle(): void
    {
        if ($this->chapter->page_sizes) {
            return;
        }

        $pageSizes = collect(range(1, $this->chapter->pages))
            ->map(function (int $page) {
                try {
                    list($width, $height) = getimagesize($this->chapter->pageOriginUrl($page));

                    return "{$width}x{$height}";
                } catch (ErrorException $e) {
                    if (Str::contains($e->getMessage(), '404 Not Found')) {
                        return '';
                    }

                    throw $e;
                }
            })
            ->implode(',');

        $this->chapter->update(['page_sizes' => $pageSizes]);
    }
}
