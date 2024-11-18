<?php

namespace App\Jobs;

use AlibabaCloud\Cdn\Cdn;
use App\Cloudflare\Cloudflare;
use App\Models\Chapter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use ToshY\BunnyNet\BaseAPI;

class RefreshChapterPage implements ShouldQueue
{
    use Queueable, Dispatchable;

    public function __construct(
        public Chapter $chapter,
        public int $page,
    ) {}

    public function handle(): void
    {
        $imagePath = $this->chapter->pageImagePath($this->page);

        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);
        }

        Cdn::v20180510()->refreshObjectCaches()->withObjectPath($this->chapter->pageOriginUrl($this->page))->request();

        app(Cloudflare::class)->purge(config('services.cloudflare.origin_zone_id'), $this->chapter->pageOriginUrl($this->page));
        app(Cloudflare::class)->purge(config('services.cloudflare.cdn_zone_id'), $this->chapter->pageCdnUrl($this->page));

        app(BaseAPI::class)->purgeUrl(['url' => $this->chapter->pageCdnUrl($this->page)]);

        DownloadChapter::dispatchSync($this->chapter, true);
    }
}
