<?php

namespace App\Console\Commands;

use App\Models\Comic;
use Illuminate\Console\Command;
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

                Storage::disk('aliyun')->put(
                    $comic->coverImagePath(),
                    $this->getCoverImageResource($comic),
                );

                $comic->update(['has_downloaded_cover' => true]);

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
