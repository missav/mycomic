<?php

namespace App\Console\Commands;

use App\Jobs\GenerateSitemap;
use Illuminate\Console\Command;

class TempCommand extends Command
{
    protected $signature = 'temp';

    protected $description = 'Temp command';

    public function handle(): void
    {
        GenerateSitemap::dispatchSync();
    }
}
