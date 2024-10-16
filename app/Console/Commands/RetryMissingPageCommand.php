<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Models\MissingPage;
use Illuminate\Console\Command;

class RetryMissingPageCommand extends Command
{
    protected $signature = 'missing:retry';

    protected $description = 'Retry missing page command command';

    public function handle(): void
    {
        $missingPageChatperIds = MissingPage::groupBy('chapter_id')->pluck('chapter_id');

        Chapter::whereIn('id', $missingPageChatperIds)->update(['has_downloaded_pages' => false]);

        MissingPage::truncate();

        $this->info('Retried all missing pages');
    }
}
