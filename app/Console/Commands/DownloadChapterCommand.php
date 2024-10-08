<?php

namespace App\Console\Commands;

use App\Jobs\DownloadChapter;
use App\Models\Chapter;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class DownloadChapterCommand extends Command
{
    protected $signature = 'chapter:download {id?} {--max=200}';

    protected $description = 'Download chapter command';

    public function handle(): void
    {
        Chapter::query()
            ->when($this->argument('id'), fn (Builder $query, int $id) => $query->where('id', $id))
            ->where('has_downloaded_pages', false)
            ->limit($this->option('max'))
            ->get()
            ->each(function (Chapter $chapter) {
                DownloadChapter::dispatch($chapter);

                $this->info("Downloaded chapter #{$chapter->id}");
            });

        $this->info('Downloaded all chapters');
    }
}
