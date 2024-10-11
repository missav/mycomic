<?php

namespace App\Concerns;

use App\Scrapfly\Scrapfly;

trait WithScraper
{
    public function scrap(string $url, ?string $country = null): string
    {
        return app(Scrapfly::class)->scrap($url, country: $country)['result']['content'];
    }
}
