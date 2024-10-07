<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Chapter;
use App\Models\Comic;
use App\Models\Tag;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ImportComicCommand extends Command
{
    protected $signature = 'comic:import {start=1} {limit=1}';

    protected $description = 'Import comics command';

    public function handle(): void
    {
        $start = $currentId = $this->argument('start');
        $limit = $this->argument('limit');
        $max = $start + $limit - 1;

        do {
            $this->info("Importing comic #{$currentId}");

            try {
                $source = $this->getComicSource($currentId);
            } catch (RequestException $e) {
                if ($e->getCode() === 404) {
                    break;
                }

                throw $e;
            }

            $comicData = $this->getComicDataFromSource($source);

            $comic = Comic::updateOrCreate(['id' => $currentId], [
                'name' => $comicData->get('name'),
                'original_name' => $comicData->get('original_name'),
                'aliases' => implode('|', $comicData->get('aliases')) ?: null,
                'description' => $comicData->get('description'),
                'country' => $comicData->get('country'),
                'audience' => $comicData->get('audience'),
                'year' => $comicData->get('year'),
                'initial' => $comicData->get('initial'),
                'is_finished' => $comicData->get('is_finished'),
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

            $this->info("Imported comic #{$currentId}");

            $currentId++;
        } while ($currentId <= $max);

        $this->info('Imported all comics');
    }

    protected function getComicSource(int $id): string
    {
        return Http::retry(5, 1000)
            ->connectTimeout(30)
            ->timeout(30)
            ->get("https://tw.manhuagui.com/comic/{$id}/")
            ->body();
    }

    protected function getComicDataFromSource(string $source): Collection
    {
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
                    '出品年代：' => (int) $node->siblings()->text(),
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
                    '漫畫狀態：' => $node->siblings()->text() === '已完結',
                    default => null,
                },
            ];
        });

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

        return collect($segments)
            ->reject(fn (array $segment) => $segment['key'] === null)
            ->mapWithKeys(fn (array $segment) => [$segment['key'] => $segment['value']])
            ->put('name', $crawler->filter('.book-title h1')->text())
            ->put('original_name', $crawler->filter('.book-title h2')->text() ?: null)
            ->put('audience', str($crawler->filter('.crumb a:nth-of-type(3)')->attr('href'))->explode('/')->get(2))
            ->put('description', $crawler->filter('#intro-all')->text() ?: null)
            ->put('chapters', $chapters);
    }

    protected function getFirstIntegerFromString(string $text): int
    {
        preg_match_all('!\d+!', $text, $matches);

        return (int) ($matches[0][0] ?? 0);
    }
}
