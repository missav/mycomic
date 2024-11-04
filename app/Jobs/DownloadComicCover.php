<?php

namespace App\Jobs;

use App\Exceptions\MissingComicCoverException;
use App\Models\Comic;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadComicCover
{
    use Queueable;

    public function __construct(
        protected Comic $comic,
    ) {}

    public function handle(): void
    {
        if ($this->comic->has_downloaded_cover) {
            return;
        }

        if (Storage::exists($this->comic->coverImagePath())) {
            $this->comic->update(['has_downloaded_cover' => true]);
            return;
        }

        try {
            Storage::put(
                $this->comic->coverImagePath(),
                $this->getCoverImageResource($this->comic),
            );
        } catch (RequestException $e) {
            if ($e->getCode() === 404) {
                $this->comic->update(['has_downloaded_cover' => -1]);
                return;
            }

            throw $e;
        }

        if (Storage::exists($this->comic->coverImagePath())) {
            $this->comic->update(['has_downloaded_cover' => true]);
        } else {
            throw new MissingComicCoverException;
        }
    }

    protected function getCoverImageResource(Comic $comic): mixed
    {
        return Http::withHeader('referer', 'https://tw.manhuagui.com/')
            ->get("https://cf.mhgui.com/cpic/h/{$comic->id}.jpg")
            ->throw()
            ->resource();
    }
}
