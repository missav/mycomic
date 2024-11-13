<?php

namespace App\Providers;

use App\Models\Comic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::unguard();
        Model::preventLazyLoading();

        Relation::morphMap(['comic' => Comic::class]);

        $this->registerHttpMacros();
        $this->registerRequestMacros();
    }

    protected function registerHttpMacros(): void
    {
        Http::macro('proxy', function (): PendingRequest {
            return Http::withoutVerifying()->withOptions([
                'proxy' => [
                    'http'  => config('app.proxy_url'),
                    'https'  => config('app.proxy_url'),
                ],
            ]);
        });
    }

    protected function registerRequestMacros(): void
    {
        Request::macro('append', function (string $key, ?string $value, array $except = ['page']): array {
            $data = request()->except($except);

            Arr::set($data, $key, $value);

            return $data;
        });
    }
}
