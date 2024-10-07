<?php

namespace App\Console\Commands;

use App\Models\Comic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class CheckComicUpdateCommand extends Command
{
    protected $signature = 'comic:check {day=3}';

    protected $description = 'Check comic update command';

    public function handle(): void
    {
        $source = $this->getComicUpdateSource($this->argument('day'));

        $crawler = new Crawler($source);

        $outdatedComicIds = $crawler->filter('.latest-list a')->each(fn (Crawler $node) =>
            (int) str($node->attr('href'))->explode('/')->get(2)
        );

        $updated = Comic::whereIn('id', $outdatedComicIds)->update(['is_outdated' => true]);

        $this->info("Marked {$updated} comics as outdated");
    }

    protected function getComicUpdateSource(int $day): string
    {
        $day++;

        return Http::retry(5, 1000)
            ->connectTimeout(30)
            ->timeout(30)
            ->get("https://tw.manhuagui.com/update/d{$day}.html")
            ->body();
    }
}
