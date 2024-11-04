<?php

namespace App\Console\Commands;

use App\Models\Comic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use stdClass;

class SyncPageViewCommand extends Command
{
    const MINIMUM_VIEWS = 5;

    protected $signature = 'comic:sync-page-views';

    protected $description = 'Sync comic page views data command';

    public function handle(): void
    {
        $query = DB::table('page_views')
            ->select('comic_id', DB::raw('SUM(views) AS total_views'))
            ->where('views', '>=', static::MINIMUM_VIEWS)
            ->groupBy('comic_id')
            ->orderBy('comic_id');

        $periods = [
            1 => $query->clone()->where('created_at', '>=', now()->subDay()->toDateString()),
            7 => $query->clone()->where('created_at', '>=', now()->subDays(7)->toDateString()),
            30 => $query->clone()->where('created_at', '>=', now()->subDays(30)->toDateString()),
        ];

        foreach ($periods as $period => $query) {
            $field = "views_{$period}d";
            $count = 0;
            $page = 1;

            Comic::query()->update([$field => 0]);

            $this->info("Reset comic {$field} field");

            while (true) {
                $pageViews = $query->forPage($page++, 10000)->get();

                if ($pageViews->isEmpty()) {
                    break;
                }

                $values = $pageViews->map(fn (stdClass $pageView) => "({$pageView->comic_id}, {$pageView->total_views})")->implode(', ');

                DB::unprepared("INSERT IGNORE INTO `comics` (`id`, `{$field}`) VALUES {$values} ON DUPLICATE KEY UPDATE `{$field}` = VALUES(`{$field}`)");

                $count += $pageViews->count();

                $this->info("Processed {$count} comics for {$period} days page views");
            }
        }

        $this->info('Synced all comic page views data');
    }
}
