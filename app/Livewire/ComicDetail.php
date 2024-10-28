<?php

namespace App\Livewire;

use App\Concerns\InteractsWithAuth;
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
    use InteractsWithAuth;

    public Comic $comic;

    public bool $hasBookmarked = false;

    public bool $isSynced = false;

    public array $availableActionsAfterLogin = ['bookmark'];

    #[Computed]
    public function recentUpdatedComics(): Collection
    {
        return Comic::with('recentChapter')->orderByDesc('last_updated_on')->orderBy('id')->take(10)->get();
    }

    #[Computed]
    public function recentPublishedComics(): Collection
    {
        return Comic::with('recentChapter')->orderByDesc('id')->take(10)->get();
    }

    public function sync(): void
    {
        $this->isLoggedIn = (bool) user();
        $this->hasBookmarked = user() && user()->records()
                ->where('comic_id', $this->comic->id)
                ->where('has_bookmarked', true)
                ->exists();
        $this->isSynced = true;
    }

    public function view(): void
    {
        $this->comic->increment('views');
    }

    public function bookmark(): void
    {
        user()->records()->updateOrCreate([
            'comic_id' => $this->comic->id,
        ], [
            'has_bookmarked' => true,
        ]);

        $this->hasBookmarked = true;
    }

    public function unbookmark(): void
    {
        $record = user()->records()->where('comic_id', $this->comic->id)->first();

        if(! $record) {
            return;
        }

        if ($record->chapter_id) {
            $record->update(['has_bookmarked' => false]);
        } else {
            $record->delete();
        }

        $this->hasBookmarked = false;
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
