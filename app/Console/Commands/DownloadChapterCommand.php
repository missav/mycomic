<?php

namespace App\Console\Commands;

use App\Jobs\DownloadChapter;
use App\Models\Chapter;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class DownloadChapterCommand extends Command
{
    protected $signature = 'chapter:download {id?}';

    protected $description = 'Download chapter command';

    public function handle(): void
    {
        $countLockedChapters = Chapter::whereNotNull('locked_at')->count();

        Chapter::query()
            ->when($this->argument('id'), fn (Builder $query, int $id) => $query->where('id', $id))
            ->where('has_downloaded_pages', false)
            ->unless($this->argument('id'), fn (Builder $query) =>
                $query->where(fn (Builder $query) => $query
                    ->orWhereNull('locked_at')
                    ->orWhere('locked_at', '<', now()->subSeconds(1800))
                )
            )
            ->limit(2000 - $countLockedChapters)
            ->get()
            ->each(function (Chapter $chapter) {
                $chapter->lock();

                if ($this->argument('id')) {
                    DownloadChapter::dispatchSync($chapter);
                } else {
                    DownloadChapter::dispatch($chapter);
                }

                $this->info("Downloaded chapter #{$chapter->id}");
            });

        $this->info('Downloaded all chapters');
    }
}
