<?php

namespace App\Livewire;

use App\Concerns\InteractsWithAuth;
use App\Concerns\WithSidebar;
use App\Models\Comic;
use App\Recombee\Recombee;
use App\Seo;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Recombee\RecommApi\Requests\DeleteBookmark;
use Spatie\SchemaOrg\MultiTypedEntity;
use Spatie\SchemaOrg\Schema;

class ComicDetail extends Component
{
    use InteractsWithAuth, WithSidebar;

    public Comic $comic;

    public bool $hasBookmarked = false;

    public ?int $recentChapterId = null;

    public bool $isSynced = false;

    public array $availableActionsAfterLogin = ['bookmark'];

    public function sync(): void
    {
        $this->comic->increment('views');

        $record = user()?->records()->where('comic_id', $this->comic->id)->first();

        $this->isLoggedIn = (bool) user();
        $this->hasBookmarked = $record && $record->has_bookmarked;
        $this->recentChapterId = $record ? $record->chapter_id : null;
        $this->isSynced = true;
    }

    public function bookmark(): void
    {
        user()->records()->updateOrCreate([
            'comic_id' => $this->comic->id,
        ], [
            'has_bookmarked' => true,
        ]);

        $this->hasBookmarked = true;

        $this->dispatch('comic-bookmarked', comicId: $this->comic->id);
    }

    public function unbookmark(): void
    {
        $record = user()->records()->where('comic_id', $this->comic->id)->first();

        if ($record->chapter_id) {
            $record->update(['has_bookmarked' => false]);
        } else {
            $record->delete();
        }

        $this->hasBookmarked = false;

        Recombee::send(new DeleteBookmark(user()->id, $record->comic_id));
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
