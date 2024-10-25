<?php

namespace App\Http\Controllers;

use App\Concerns\InteractsWithSitemap;
use App\Models\Chapter;
use App\Models\Comic;
use Illuminate\Http\Response;
use Spatie\Sitemap\SitemapIndex;

class BaseSitemap
{
    use InteractsWithSitemap;

    const int URL_PER_SITEMAP = 1000;

    public function __invoke(): Response
    {
        $sitemapIndex = SitemapIndex::create();

        $sitemapIndex->add(route('sitemaps.pages.index'));

        foreach (static::modelQueries() as $modelClass => $query) {
            $maxPage = ceil($query->count() / static::URL_PER_SITEMAP);
            $model = str($modelClass)->classBasename()->plural()->lower()->__toString();

            for ($page = 1; $page <= $maxPage; $page++) {
                $sitemapIndex->add(route('sitemaps.models.index', ['modelId' => $model, 'page' => $page]));
            }
        }

        return response($sitemapIndex->render(), headers: [
            'Content-Type' => 'text/xml',
        ]);
    }

    public static function modelQueries(): array
    {
        return [
            'comics' => Comic::query()->orderBy('id'),
            'chapters' => Chapter::query()->orderBy('id'),
        ];
    }
}
