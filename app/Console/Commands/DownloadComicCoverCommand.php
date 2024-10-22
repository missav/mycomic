<?php

namespace App\Console\Commands;

use App\Jobs\DownloadComicCover;
use App\Models\Comic;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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

                DownloadComicCover::dispatch($comic);

                $this->info("Downloaded comic cover #{$comic->id}");
            }));

        $this->info('Downloaded all comic covers');
    }
}
