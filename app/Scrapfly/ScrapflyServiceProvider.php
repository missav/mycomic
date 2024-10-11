<?php

namespace App\Scrapfly;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ScrapflyServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(Scrapfly::class, function () {
            return new Scrapfly(config('services.scrapfly.api_key'));
        });
    }

    public function provides(): array
    {
        return [Scrapfly::class];
    }
}
