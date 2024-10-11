<?php

namespace App\Console\Commands;

use App\Concerns\WithScraper;
use App\Models\Comic;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class CheckComicUpdateCommand extends Command
{
    use WithScraper;

    protected $signature = 'comic:check {day=3}';

    protected $description = 'Check comic update command';

    public function handle(): void
    {
        $day = (int) $this->argument('day') + 1;

        $source = $this->scrap("https://tw.manhuagui.com/update/d{$day}.html", '-jp');

        $crawler = new Crawler($source);

        $outdatedComicIds = $crawler->filter('.latest-list a')->each(fn (Crawler $node) =>
            (int) str($node->attr('href'))->explode('/')->get(2)
        );

        $updated = Comic::whereIn('id', $outdatedComicIds)->update(['is_outdated' => true]);

        $this->info("Marked {$updated} comics as outdated");
    }
}
