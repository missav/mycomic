<?php

namespace App\Livewire;

use App\Models\Comic;
use App\Seo;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\SchemaOrg\MultiTypedEntity;
use Spatie\SchemaOrg\Schema;

class ComicDetail extends Component
{
    public Comic $comic;

    #[Computed]
    public function recentUpdatedComics(): Collection
    {
        return Comic::with('recentChapter')->orderByDesc('last_updated_on')->take(10)->get();
    }

    #[Computed]
    public function recentPublishedComics(): Collection
    {
        return Comic::with('recentChapter')->orderByDesc('id')->take(12)->get();
    }

    public function view(): void
    {
        $this->comic->increment('views');
    }

    public function render(): View
    {
        Seo::title($this->comic->name());
        Seo::description($this->comic->description());
        Seo::keywords($this->comic->keywords()->all());
        Seo::authors($this->comic->authors->pluck('name')->all());
        Seo::image($this->comic->coverCdnUrl());

        $mte = new MultiTypedEntity;

        $mte->breadcrumbList()
            ->itemListElement([
                Schema::listItem()->position(1)->name(__('Comic database'))->item(
                    Schema::thing()->url(localizedRoute('comics.index'))
                ),
                Schema::listItem()->position(2)->name($this->comic->name())->item(
                    Schema::thing()->url(localizedRoute('comics.view', ['comic' => $this->comic]))
                ),
            ]);

        $mte->comicSeries()
            ->audience(Schema::audience()->name($this->comic->audience->text()))
            ->author($this->comic->authors->pluck('name')->implode(', '))
            ->countryOfOrigin(Schema::country()->name($this->comic->country->text()))
            ->datePublished(Carbon::make("{$this->comic->year}-01-01"))
            ->keywords($this->comic->tags->pluck('name')->all())
            ->thumbnailUrl($this->comic->coverCdnUrl())
            ->alternateName($this->comic->original_name)
            ->description($this->comic->description())
            ->image($this->comic->coverCdnUrl())
            ->name($this->comic->name())
            ->url($this->comic->url());

        Seo::jsonLdScript($mte->toScript());

        return view('livewire.comic-detail');
    }
}
