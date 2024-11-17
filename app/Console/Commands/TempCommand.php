<?php

namespace App\Console\Commands;

use App\Jobs\FetchChapterPageSizes;
use App\Models\Chapter;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class TempCommand extends Command
{
    protected $signature = 'temp';

    protected $description = 'Temp command';

    public function handle(): void
    {
        Chapter::query()
            ->whereNull('page_sizes')
            ->where('pages', '<=', 50)
            ->chunkById(1000, function (Collection $chapters) {
                $chapters->each(function (Chapter $chapter) {
                    FetchChapterPageSizes::dispatch($chapter);

                    $this->info("Queued chapter #{$chapter->id}");
                });
            });

        $this->info('Queued all chapter');
    }
}
