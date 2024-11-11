<?php

namespace App\Livewire;

use App\Concerns\SyncUserUuid;
use App\Concerns\WithUserUuid;
use App\Models\Chapter;
use App\Models\Record;
use App\Seo;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\SchemaOrg\MultiTypedEntity;
use Spatie\SchemaOrg\Schema;

class ChapterReader extends Component
{
    use SyncUserUuid, WithUserUuid;

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

    public function sync(): void
    {
        $this->syncUserUuid();

        Record::updateOrCreate([
            'user_id' => $this->getUserUuid(),
            'comic_id' => $this->chapter->comic->id,
        ], [
            'chapter_id' => $this->chapter->id,
            'updated_at' => now(),
        ]);
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
                    Schema::thing()
                        ->identifier(localizedRoute('comics.index'))
                        ->url(localizedRoute('comics.index'))
                ),
                Schema::listItem()->position(2)->name($this->chapter->comic->name())->item(
                    Schema::thing()
                        ->identifier(localizedRoute('comics.view', ['comic' => $this->chapter->comic]))
                        ->url(localizedRoute('comics.view', ['comic' => $this->chapter->comic]))
                ),
                Schema::listItem()->position(3)->name($this->chapter->title())->item(
                    Schema::thing()
                        ->identifier(localizedRoute('chapters.view', ['chapter' => $this->chapter]))
                        ->url(localizedRoute('chapters.view', ['chapter' => $this->chapter]))
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

        $pages = collect(range(1, $this->chapter->pages))->map(fn (int $page) => [
            'number' => $page,
            'url' => $this->chapter->pageCdnUrl($page),
            'viewable' => false,
            'show' => false,
        ])->all();

        return view('livewire.chapter-reader', [
            'pages' => $pages,
        ]);
    }
}
