<?php

namespace App\Console\Commands;

use App\Models\Comic;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class TempCommand extends Command
{
    protected $signature = 'temp';

    protected $description = 'Temp command';

    public function handle(): void
    {
        Comic::query()
            ->whereNull('recent_chapter_id')
            ->chunkById(1000, function (Collection $comics) {
                $comics->each(function (Comic $comic) {
                    if ($recentChapter = $comic->recentChapter) {
                        $comic->update([
                            'recent_chapter_id' => $recentChapter->id,
                            'recent_chapter_title' => $recentChapter->title,
                        ]);
                    }
                    $this->info("Updated {$comic->id}");
                });
            });

        $this->info('Done');
    }
}
