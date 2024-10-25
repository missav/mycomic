<?php

namespace App\Jobs;

use App\Enums\Locale;
use App\Models\Chapter;
use App\Models\Comic;
use App\Models\WorkerLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap implements ShouldQueue
{
    use Dispatchable, Queueable;

    const int URL_PER_SITEMAP = 1000;

    public function handle(): void
    {
        //$this->generatePageSitemap();
        //$this->generateModelSitemaps();
        $this->generateSitemapIndex();
    }

    protected function generatePageSitemap(): void
    {
        $sitemap = Sitemap::create();

        $this->addLocalizedRoute($sitemap, 'home');
        $this->addLocalizedRoute($sitemap, 'comics.index');

        dd($sitemap->render());

        $sitemap->writeToDisk('aliyun', 'sitemap_pages.xml');
    }

    protected function generateSitemapIndex(): void
    {
        $sitemapIndex = SitemapIndex::create();

        $sitemapIndex->add(url('sitemap_pages.xml'));

        foreach (static::modelQuery() as $modelClass => $query) {
            $maxPage = ceil($query->count() / static::URL_PER_SITEMAP);
            $type = str($modelClass)->classBasename()->plural()->lower()->__toString();

            for ($i = 1; $i <= $maxPage; $i++) {
                $sitemapIndex->add(url("sitemap_{$type}_{$i}.xml"));
            }
        }

        dd($sitemapIndex->render());

        $sitemapIndex->writeToDisk('aliyun', 'sitemap.xml');
    }

    protected function addLocalizedRoute(Sitemap $sitemap, string $name, array $parameters = []): void
    {
        foreach (Locale::cases() as $locale) {
            $url = Url::create(localizedRoute($name, $parameters, locale: $locale));

            foreach (Locale::cases() as $alternateLocale) {
                $url->addAlternate(localizedRoute($name, $parameters, locale: $alternateLocale), $locale->code());
            }

            $sitemap->add($url);
        }
    }

    public static function modelQuery(): array
    {
        return [
            Comic::class => Comic::query(),
            Chapter::class => Chapter::query(),
        ];
    }
}
