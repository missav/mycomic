<?php

namespace App\Console\Commands;

use App\Exceptions\MissingComicCoverException;
use App\Models\Comic;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadComicCoverCommand extends Command
{
    protected $signature = 'comic:download-cover';

    protected $description = 'Download comic cover command';

    public function handle(): void
    {
        Comic::query()
            ->where('has_downloaded_cover', false)
            ->chunkById(100, fn (Collection $comics) => $comics->each(function (Comic $comic) {
                $this->info("Downloading comic cover #{$comic->id}");

                if (Storage::exists($comic->coverImagePath())) {
                    $comic->update(['has_downloaded_cover' => true]);
                    return;
                }

                try {
                    Storage::put(
                        $comic->coverImagePath(),
                        $this->getCoverImageResource($comic),
                    );
                } catch (RequestException $e) {
                    if ($e->getCode() === 404) {
                        $comic->update(['has_downloaded_cover' => -1]);
                        $this->error("Missing comic cover #{$comic->id}");
                        return;
                    }

                    throw $e;
                }

                if (Storage::exists($comic->coverImagePath())) {
                    $comic->update(['has_downloaded_cover' => true]);
                } else {
                    throw new MissingComicCoverException;
                }

                $this->info("Downloaded comic cover #{$comic->id}");
            }));

        $this->info('Downloaded all comic covers');
    }

    protected function getCoverImageResource(Comic $comic): mixed
    {
        return Http::withHeader('referer', 'https://tw.manhuagui.com/')
            ->get("https://cf.mhgui.com/cpic/h/{$comic->id}.jpg")
            ->resource();
    }
}
