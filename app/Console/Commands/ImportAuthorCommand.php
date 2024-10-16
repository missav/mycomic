<?php

namespace App\Console\Commands;

use App\Concerns\WithScraper;
use App\Models\Author;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ImportAuthorCommand extends Command
{
    use WithScraper;

    protected $signature = 'author:import';

    protected $description = 'Import author command';

    public function handle(): void
    {
        Author::query()
            ->whereNull('description')
            ->chunkById(100, fn (Collection $authors) =>
                $authors->each(function (Author $author) {
                    $this->info("Importing author #{$author->id}");

                    try {
                        $source = $this->scrap(Author::sourceUrl($author->id));
                    } catch (RequestException $e) {
                        if ($e->getCode() === 404) {
                            Author::updateOrCreate(['id' => $author->id], [
                                'original_name' => null,
                                'country' => null,
                                'initial' => null,
                                'description' => '',
                            ]);
                            $this->error("Missing author #{$author->id}");
                            return;
                        }

                        throw $e;
                    }

                    $authorData = $this->getAuthorDataFromSource($source);

                    Author::updateOrCreate(['id' => $author->id], [
                        'original_name' => implode('|', $authorData->get('original_name')) ?: null,
                        'country' => $authorData->get('country'),
                        'initial' => $authorData->get('initial'),
                        'description' => $authorData->get('description'),
                    ]);

                    $this->info("Imported author #{$author->id}");
                })
            );

        $this->info('Imported all authors');
    }

    protected function getAuthorDataFromSource(string $source): Collection
    {
        $crawler = new Crawler($source);

        $segments = $crawler->filter('.info p')->each(function (Crawler $node) {
            $label = $node->filter('em')->text();

            return [
                'key' => match($label) {
                    '作者別名：' => 'original_name',
                    '所屬地區：' => 'country',
                    '字 母：' => 'initial',
                    default => null,
                },
                'value' => match($label) {
                    '作者別名：' => $node
                        ->filter('a')
                        ->reduce(fn (Crawler $node) => $node->nodeName() !== 'em')
                        ->each(fn (Crawler $node) => $node->text()),
                    '所屬地區：' => str($node->filter('a')->attr('href'))->explode('/')->get(2),
                    '字 母：' => strtolower($node->filter('a')->text()),
                    default => null,
                },
            ];
        });

        $paragraphs = $crawler
            ->filter('#intro-all > div > p')
            ->each(fn (Crawler $node) => $node->text());

        $description = collect($paragraphs)
            ->map(fn (string $paragraph) => Str::trim($paragraph))
            ->filter()
            ->implode("\n");

        return collect($segments)
            ->reject(fn (array $segment) => $segment['key'] === null)
            ->mapWithKeys(fn (array $segment) => [$segment['key'] => $segment['value']])
            ->put('description', $description);
    }
}
