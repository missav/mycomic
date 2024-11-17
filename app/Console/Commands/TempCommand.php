<?php

namespace App\Console\Commands;

use App\Jobs\FetchChapterPageSizes;
use App\Models\Chapter;
use Illuminate\Console\Command;

class TempCommand extends Command
{
    protected $signature = 'temp';

    protected $description = 'Temp command';

    public function handle(): void
    {
        Chapter::query()
            ->whereNull('page_sizes')
            ->limit(100)
            ->get()
            ->each(function (Chapter $chapter) {
                FetchChapterPageSizes::dispatchSync($chapter);

                $this->info("Queued chapter #{$chapter->id}");
            });
    }
}
