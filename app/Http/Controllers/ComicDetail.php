<?php

namespace App\Http\Controllers;

use App\Concerns\WithPresetComics;
use App\LinkHeader;
use App\Models\Comic;
use App\Seo;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Spatie\SchemaOrg\MultiTypedEntity;
use Spatie\SchemaOrg\Schema;

class ComicDetail
{
    use WithPresetComics;

    public function __invoke(Comic $comic): View
    {
        LinkHeader::addPreconnect($comic->coverCdnUrl());

        Seo::title($comic->name());
        Seo::description($comic->description());
        Seo::keywords($comic->keywords()->all());
        Seo::authors($comic->authors->pluck('name')->all());
        Seo::image($comic->coverCdnUrl());

        $mte = new MultiTypedEntity;

        $mte->breadcrumbList()
            ->itemListElement([
                Schema::listItem()->position(1)->name(__('Comic database'))->item(
                    Schema::thing()
                        ->identifier(localizedRoute('comics.index'))
                        ->url(localizedRoute('comics.index'))
                ),
                Schema::listItem()->position(2)->name($comic->name())->item(
                    Schema::thing()
                        ->identifier(localizedRoute('comics.view', ['comic' => $comic]))
                        ->url(localizedRoute('comics.view', ['comic' => $comic]))
                ),
            ]);

        $mte->comicSeries()
            ->audience(Schema::audience()->name($comic->audience->text()))
            ->author($comic->authors->pluck('name')->map(fn (string $name) => Schema::person()->name($name))->all())
            ->countryOfOrigin(Schema::country()->name($comic->country->text()))
            ->datePublished(Carbon::make("{$comic->year}-01-01"))
            ->keywords($comic->tags->pluck('name')->all())
            ->thumbnailUrl($comic->coverCdnUrl())
            ->alternateName($comic->original_name)
            ->description($comic->description())
            ->image($comic->coverCdnUrl())
            ->name($comic->name())
            ->url($comic->url());

        Seo::jsonLdScript($mte->toScript());

        $reviews = $comic->reviews()
            ->with('user')
            ->whereNotNull('text')
            ->where('text', '!=', '')
            ->latest()
            ->limit(50)
            ->get();

        return view('comic-detail', [
            'comic' => $comic,
            'reviews' => $reviews,
            'dailyRankComics' => $this->dailyRankComics(),
            'weeklyRankComics' => $this->weeklyRankComics(),
            'monthlyRankComics' => $this->monthlyRankComics(),
            'allTimeRankComics' => $this->allTimeRankComics(),
            'recentUpdatedComics' => $this->recentUpdatedComics(),
            'recentPublishedComics' => $this->recentPublishedComics(),
        ]);
    }
}
