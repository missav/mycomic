<?php

namespace App\Jobs;

use App\Models\Chapter;
use ErrorException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class FetchChapterPageSizes implements ShouldQueue
{
    use Queueable, Dispatchable;

    public function __construct(
        public Chapter $chapter,
    ) {
        $this->onQueue('heavy');
    }

    public function handle(): void
    {
        if ($this->chapter->page_sizes) {
            return;
        }

        $pageSizes = collect(range(1, $this->chapter->pages))
            ->map(function (int $page) {
                $getSize = function () use ($page) {
                    list($width, $height) = getimagesize($this->chapter->pageOriginUrl($page));

                    return "{$width}x{$height}";
                };

                try {
                    return $getSize();
                } catch (ErrorException $e) {
                    if (Str::contains($e->getMessage(), '404 Not Found')) {
                        return '';
                    }

                    if (Str::contains($e->getMessage(), 'getimagesize(): Error reading from')) {
                        RefreshChapterPage::dispatchSync($this->chapter, $page);

                        return $getSize();
                    }

                    throw $e;
                }
            })
            ->implode(',');

        $this->chapter->update(['page_sizes' => $pageSizes]);
    }
}
