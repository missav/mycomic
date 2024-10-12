<?php

namespace App\Concerns;

use App\Scrapfly\Scrapfly;
use App\Scrapfly\ScrapflyRequestException;
use Exception;

trait WithScraper
{
    public function scrap(string $url): string
    {
        return retry(5, fn () => app(Scrapfly::class)->scrap($url)['result']['content'], 300, fn (Exception $e) =>
            ! ($e instanceof ScrapflyRequestException && $e->getCode() === 404)
        );
    }
}
