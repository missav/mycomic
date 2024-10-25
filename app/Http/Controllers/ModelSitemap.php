<?php

namespace App\Http\Controllers;

use App\Concerns\InteractsWithSitemap;
use App\Enums\Locale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class ModelSitemap
{
    use InteractsWithSitemap;

    public function __invoke(string $modelId, int $page): Response
    {
        $modelBinding = Str::singular($modelId);

        $sitemap = Sitemap::create();

        $modelQuery = $this->modelQuery($modelId);

        $models = $modelQuery->forPage($page, BaseSitemap::URL_PER_SITEMAP)->get();

        if ($models->isEmpty()) {
            abort(404);
        }

        foreach (Locale::cases() as $locale) {
            foreach ($models as $model) {
                $url = Url::create(localizedRoute("{$modelId}.view", [$modelBinding => $model], locale: $locale));

                foreach (Locale::cases() as $alternateLocale) {
                    $url->addAlternate(
                        localizedRoute("{$modelId}.view", [$modelBinding => $model], locale: $alternateLocale),
                        $alternateLocale->code(),
                    );
                }

                $sitemap->add($url);
            }
        }

        return response($sitemap->render(), headers: [
            'Content-Type' => 'text/xml',
        ]);
    }

    protected function modelQuery(string $modelId): Builder
    {
        $modelQueries = BaseSitemap::modelQueries();

        return collect($modelQueries)->firstOrFail(fn (Builder $query, string $modelQueryKey) =>
            $modelQueryKey === $modelId
        );
    }
}
