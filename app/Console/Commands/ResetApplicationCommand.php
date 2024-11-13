<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetApplicationCommand extends Command
{
    protected $signature = 'reset';

    protected $description = 'Reset application command';

    public function handle(): void
    {
        if (! $this->confirm('Are you sure to reset the application?')) {
            return;
        }

        DB::table('cache')->truncate();
        $this->info('Truncated cache');

        DB::table('cache_locks')->truncate();
        $this->info('Truncated cache_locks');

        DB::table('failed_jobs')->truncate();
        $this->info('Truncated failed_jobs');

        DB::table('page_views')->truncate();
        $this->info('Truncated page_views');

        DB::table('password_reset_tokens')->truncate();
        $this->info('Truncated password_reset_tokens');

        DB::table('records')->truncate();
        $this->info('Truncated records');

        DB::table('reviews')->truncate();
        $this->info('Truncated reviews');

        DB::table('sessions')->truncate();
        $this->info('Truncated sessions');

        DB::table('users')->truncate();
        $this->info('Truncated users');

        DB::table('comics')->update([
            'average_rating' => 0,
            'views' => 0,
            'views_1d' => 0,
            'views_7d' => 0,
            'views_30d' => 0,
            'has_submitted_recommendation' => 0,
        ]);
        $this->info('Reset comic stats');
    }
}
