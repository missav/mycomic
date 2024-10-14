<?php

namespace App\Concerns;

use Exception;
use Illuminate\Support\Facades\Http;

trait WithScraper
{
    public function scrap(string $url): string
    {
        return Http::proxy()
            ->retry(10, 500, fn (Exception $e) => $e->getCode() !== 404)
            ->get($url)
            ->throw()
            ->body();
    }
}
