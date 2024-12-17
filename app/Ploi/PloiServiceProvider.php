<?php

namespace App\Ploi;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PloiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(Ploi::class, function () {
            return new Ploi(config('services.ploi.token'));
        });
    }

    public function provides(): array
    {
        return [Ploi::class];
    }
}
