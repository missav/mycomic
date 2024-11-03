<?php

namespace App\Console\Commands;

use App\Models\Comic;
use Illuminate\Console\Command;

class TempCommand extends Command
{
    protected $signature = 'temp';

    protected $description = 'Temp command';

    public function handle(): void
    {
        dd(Comic::find(53969)->reviewsByRating());

        Comic::find(53969)->reviews()->create([
            'user_id' => '250a1ed3-70a3-404f-b330-bb2cb4fe7e6c',
            'rating' => 4,
        ]);
    }
}
