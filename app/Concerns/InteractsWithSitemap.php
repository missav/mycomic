<?php

namespace App\Concerns;

use App\Enums\Locale;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

trait InteractsWithSitemap
{
    protected function addLocalizedRoute(Sitemap $sitemap, string $name, array $parameters = []): void
    {
        foreach (Locale::cases() as $locale) {
            $url = Url::create(localizedRoute($name, $parameters, locale: $locale));

            foreach (Locale::cases() as $alternateLocale) {
                $url->addAlternate(
                    localizedRoute($name, $parameters, locale: $alternateLocale), $locale->code(),
                );
            }

            $sitemap->add($url);
        }
    }
}
