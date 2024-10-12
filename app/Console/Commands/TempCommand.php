<?php

namespace App\Console\Commands;

use App\Jobs\DownloadChapter;
use App\Models\Chapter;
use Illuminate\Console\Command;

class TempCommand extends Command
{
    protected $signature = 'temp';

    protected $description = 'Temp command';

    public function handle(): void
    {
        dd(DownloadChapter::dispatchSync(Chapter::find(1)));
    }
}
