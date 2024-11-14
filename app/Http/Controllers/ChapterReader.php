<?php

namespace App\Http\Controllers;

use App\Concerns\WithUserUuid;
use App\Models\Chapter;
use App\Seo;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Spatie\SchemaOrg\MultiTypedEntity;
use Spatie\SchemaOrg\Schema;

class ChapterReader
{
    use WithUserUuid;

    public function __invoke(Chapter $chapter): View
    {
        Seo::title($chapter->comic->name());
        Seo::description($chapter->comic->description());
        Seo::keywords($chapter->comic->keywords()->all());
        Seo::authors($chapter->comic->authors->pluck('name')->all());
        Seo::image($chapter->comic->coverCdnUrl());

        $mte = new MultiTypedEntity;

        $mte->breadcrumbList()
            ->itemListElement([
                Schema::listItem()->position(1)->name(__('Comic database'))->item(
                    Schema::thing()
                        ->identifier(localizedRoute('comics.index'))
                        ->url(localizedRoute('comics.index'))
                ),
                Schema::listItem()->position(2)->name($chapter->comic->name())->item(
                    Schema::thing()
                        ->identifier(localizedRoute('comics.view', ['comic' => $chapter->comic]))
                        ->url(localizedRoute('comics.view', ['comic' => $chapter->comic]))
                ),
                Schema::listItem()->position(3)->name($chapter->title())->item(
                    Schema::thing()
                        ->identifier(localizedRoute('chapters.view', ['chapter' => $chapter]))
                        ->url(localizedRoute('chapters.view', ['chapter' => $chapter]))
                ),
            ]);

        $mte->comicIssue()
            ->issueNumber($chapter->number)
            ->audience(Schema::audience()->name($chapter->comic->audience->text()))
            ->author($chapter->comic->authors->pluck('name')->map(fn (string $name) => Schema::person()->name($name))->all())
            ->countryOfOrigin(Schema::country()->name($chapter->comic->country->text()))
            ->datePublished(Carbon::make("{$chapter->comic->year}-01-01"))
            ->keywords($chapter->comic->tags->pluck('name')->all())
            ->position($chapter->number)
            ->thumbnailUrl($chapter->comic->coverCdnUrl())
            ->alternateName($chapter->comic->original_name)
            ->description($chapter->comic->description())
            ->image($chapter->comic->coverCdnUrl())
            ->name("{$chapter->comic->name()} - {$chapter->title()}")
            ->url($chapter->comic->url());

        Seo::jsonLdScript($mte->toScript());

        $pages = collect(range(1, $chapter->pages))->map(fn (int $page) => [
            'number' => $page,
            'url' => $chapter->pageCdnUrl($page),
        ])->all();

        return view('chapter-reader', [
            'chapter' => $chapter,
            'pages' => $pages,
            'previouUrl' => $chapter->previous()?->url(),
            'nextUrl' => $chapter->next()?->url(),
        ]);
    }
}