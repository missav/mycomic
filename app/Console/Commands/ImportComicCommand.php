<?php

namespace App\Console\Commands;

use App\Concerns\WithScraper;
use App\Models\Author;
use App\Models\Chapter;
use App\Models\Comic;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LZCompressor\LZString;
use Symfony\Component\DomCrawler\Crawler;

class ImportComicCommand extends Command
{
    use WithScraper;

    protected $signature = 'comic:import {start?} {end?}';

    protected $description = 'Import comics command';

    public function handle(): void
    {
        $start = $this->argument('start');
        $end = $this->argument('end');

        if ($start) {
            $currentId = (int) $start;
            $end = $end ? (int) $end : $currentId;
        } else {
            $currentId = Comic::max('id') + 1;
            $end = $currentId + 4;
        }

        if (! $start) {
            $this->info('Reimporting outdated comics');
            Comic::query()
                ->whereIsOutdated(true)
                ->get()
                ->each(fn (Comic $comic) =>
                    $this->importComic($comic->id)
                );
        }

        do {
            $this->info('Detecting new comics');
            $this->importComic($currentId);
            $currentId++;
        } while ($currentId <= $end);

        $this->info('Imported all comics');
    }

    protected function importComic(int $id): void
    {
        $this->info("Importing comic #{$id}");

        try {
            $source = $this->scrap(Comic::sourceUrl($id));
        } catch (RequestException $e) {
            if ($e->getCode() === 404) {
                $this->error("Missing comic #{$id}");
                return;
            }

            throw $e;
        }

        $comicData = $this->getComicDataFromSource($source);

        $comic = Comic::updateOrCreate(['id' => $id], [
            'name' => $comicData->get('name'),
            'original_name' => $comicData->get('original_name'),
            'aliases' => implode('|', $comicData->get('aliases')) ?: null,
            'description' => trim($comicData->get('description')),
            'country' => $comicData->get('country'),
            'audience' => $comicData->get('audience'),
            'year' => $comicData->get('year'),
            'initial' => $comicData->get('initial'),
            'is_finished' => $comicData->get('is_finished'),
            'is_outdated' => false,
            'last_updated_on' => $comicData->get('last_updated_on'),
        ]);

        $authorIds = collect($comicData->get('authors'))
            ->map(fn (array $author) => Author::updateOrCreate(['id' => $author['id']], ['name' => $author['text']]))
            ->pluck('id');
        $comic->authors()->sync($authorIds);

        $tagIds = collect($comicData->get('tags'))
            ->map(fn (array $tag) => Tag::updateOrCreate(['slug' => $tag['id']], ['name' => $tag['text']]))
            ->pluck('id');
        $comic->tags()->sync($tagIds);

        $comicData->get('chapters')->each(fn (array $chapterData) =>
            Chapter::updateOrCreate([
                'id' => $chapterData['id'],
            ], [
                'comic_id' => $comic->id,
                'type' => $chapterData['type'],
                'number' => $chapterData['number'],
                'title' => $chapterData['title'],
                'pages' => $chapterData['pages'],
            ])
        );

        $this->info("Imported comic #{$id}");
    }

    protected function getComicDataFromSource(string $source): Collection
    {
        if (Str::contains($source, '__VIEWSTATE')) {
            $encodedHtml = Str::betweenFirst($source, '<input type="hidden" id="__VIEWSTATE" value="', '"');
            $decodedHtml = LZString::decompressFromBase64($encodedHtml);
            $source = Str::replaceFirst('<div class="chapter cf mt16">', '<div class="chapter cf mt16">' . $decodedHtml, $source);
        }

        $crawler = new Crawler($source);

        $segments = $crawler->filter('.detail-list li strong')->each(function (Crawler $node) {
            return [
                'key' => match($node->text()) {
                    '出品年代：' => 'year',
                    '漫畫地區：' => 'country',
                    '字母索引：' => 'initial',
                    '漫畫劇情：' => 'tags',
                    '漫畫作者：' => 'authors',
                    '漫畫別名：' => 'aliases',
                    '漫畫狀態：' => 'is_finished',
                    default => null,
                },
                'value' => match($node->text()) {
                    '出品年代：' => (int) ($node->siblings()->count() > 0 ? $node->siblings()->first()->text() : null),
                    '漫畫地區：' => str($node->siblings()->attr('href'))->explode('/')->get(2),
                    '字母索引：' => strtolower($node->siblings()->text()),
                    '漫畫別名：' => $node
                        ->siblings()
                        ->reduce(fn (Crawler $node) => $node->nodeName() !== 'em')
                        ->each(fn (Crawler $node) => $node->text()),
                    '漫畫劇情：', '漫畫作者：' => $node
                        ->siblings()
                        ->reduce(fn (Crawler $node) => $node->nodeName() === 'a')
                        ->each(fn (Crawler $node) => [
                            'id' => str($node->attr('href'))->explode('/')->get(2),
                            'text' => $node->text(),
                        ]),
                    '漫畫狀態：' => $node->siblings()->count() && $node->siblings()->text() === '已完結',
                    default => null,
                },
            ];
        });

        if ($crawler->filter('.chapter h4')->count() === 0) {
            $chapters = collect($crawler->filter('.chapter-list a')->each(fn (Crawler $node) => [
                'id' => str($node->attr('href'))->afterLast('/')->before('.html')->toInteger(),
                'type' => null,
                'pages' => (int) Str::substr($node->filter('i')->text(), 0, -1),
                'title' => $title = $node->attr('title'),
                'number' => $this->getFirstIntegerFromString($title),
            ]));
        } else {
            $chapters = collect($crawler->filter('.chapter h4')->each(function (Crawler $node, int $i) {
                $type = $node->text();

                return $node->siblings()->filter('.chapter-list')->eq($i)->filter('a')->each(fn (Crawler $node) => [
                    'id' => str($node->attr('href'))->afterLast('/')->before('.html')->toInteger(),
                    'type' => $type,
                    'pages' => (int) Str::substr($node->filter('i')->text(), 0, -1),
                    'title' => $title = $node->attr('title'),
                    'number' => $this->getFirstIntegerFromString($title),
                ]);
            }))->flatten(1);
        }

        return collect($segments)
            ->reject(fn (array $segment) => $segment['key'] === null)
            ->mapWithKeys(fn (array $segment) => [$segment['key'] => $segment['value']])
            ->put('name', $crawler->filter('.book-title h1')->text())
            ->put('original_name', $crawler->filter('.book-title h2')->text() ?: null)
            ->put('audience', str($crawler->filter('.crumb a:nth-of-type(3)')->attr('href'))->explode('/')->get(2))
            ->put('description', $crawler->filter('#intro-all')->text() ?: null)
            ->put('last_updated_on', $crawler->filter('.status > span > span')->count() ? $crawler->filter('.status > span > span')->eq(1)->text() : null)
            ->put('chapters', $chapters);
    }

    protected function getFirstIntegerFromString(string $text): int
    {
        preg_match_all('!\d+!', $text, $matches);

        return (int) ($matches[0][0] ?? 0);
    }
}
