<?php

namespace App\Concerns;

use App\Scrapfly\Scrapfly;

trait WithScraper
{
    public function scrap(string $url): string
    {
        return retry(5, fn () => app(Scrapfly::class)->scrap($url)['result']['content'], 300);
    }
}
