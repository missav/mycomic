<?php

namespace App\Livewire;

use App\Models\Chapter;
use App\Seo;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\SchemaOrg\MultiTypedEntity;
use Spatie\SchemaOrg\Schema;

class ChapterReader extends Component
{
    public Chapter $chapter;

    #[Computed]
    public function previousUrl(): ?string
    {
        return $this->chapter->previous()?->url();
    }

    #[Computed]
    public function nextUrl(): ?string
    {
        return $this->chapter->next()?->url();
    }

    public function render(): View
    {
        Seo::title($this->chapter->comic->name());
        Seo::description($this->chapter->comic->description());
        Seo::keywords($this->chapter->comic->keywords()->all());
        Seo::authors($this->chapter->comic->authors->pluck('name')->all());
        Seo::image($this->chapter->comic->coverCdnUrl());

        $mte = new MultiTypedEntity;

        $mte->breadcrumbList()
            ->itemListElement([
                Schema::listItem()->position(1)->name(__('Comic database'))->item(
                    Schema::thing()->url(localizedRoute('comics.index'))
                ),
                Schema::listItem()->position(2)->name($this->chapter->comic->name())->item(
                    Schema::thing()->url(localizedRoute('comics.view', ['comic' => $this->chapter->comic]))
                ),
                Schema::listItem()->position(3)->name($this->chapter->title())->item(
                    Schema::thing()->url(localizedRoute('chapters.view', ['chapter' => $this->chapter]))
                ),
            ]);

        $mte->comicIssue()
            ->issueNumber($this->chapter->number)
            ->audience(Schema::audience()->name($this->chapter->comic->audience->text()))
            ->author($this->chapter->comic->authors->pluck('name')->implode(', '))
            ->countryOfOrigin(Schema::country()->name($this->chapter->comic->country->text()))
            ->datePublished(Carbon::make("{$this->chapter->comic->year}-01-01"))
            ->keywords($this->chapter->comic->tags->pluck('name')->all())
            ->position($this->chapter->number)
            ->thumbnailUrl($this->chapter->comic->coverCdnUrl())
            ->alternateName($this->chapter->comic->original_name)
            ->description($this->chapter->comic->description())
            ->image($this->chapter->comic->coverCdnUrl())
            ->name("{$this->chapter->comic->name()} - {$this->chapter->title()}")
            ->url($this->chapter->comic->url());

        Seo::jsonLdScript($mte->toScript());

        return view('livewire.chapter-reader');
    }
}
