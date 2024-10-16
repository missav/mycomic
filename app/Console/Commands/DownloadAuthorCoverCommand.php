<?php

namespace App\Console\Commands;

use App\Concerns\WithScraper;
use App\Exceptions\MissingAuthorCoverException;
use App\Models\Author;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class DownloadAuthorCoverCommand extends Command
{
    use WithScraper;

    protected $signature = 'author:download-cover';

    protected $description = 'Download author cover command';

    public function handle(): void
    {
        Author::query()
            ->where('has_downloaded_cover', false)
            ->chunkById(100, fn (Collection $authors) => $authors->each(function (Author $author) {
                $this->info("Downloading author cover #{$author->id}");

                if (Storage::exists($author->coverImagePath())) {
                    $author->update(['has_downloaded_cover' => true]);
                    return;
                }

                try {
                    Storage::disk('aliyun')->put(
                        $author->coverImagePath(),
                        $this->getCoverImageResource($author),
                    );
                } catch (RequestException $e) {
                    if ($e->getCode() === 404) {
                        $author->update(['has_downloaded_cover' => -1]);
                        $this->error("Missing author cover #{$author->id}");
                        return;
                    }

                    throw $e;
                }

                if (Storage::exists($author->coverImagePath())) {
                    $author->update(['has_downloaded_cover' => true]);
                } else {
                    throw new MissingAuthorCoverException;
                }

                $this->info("Downloaded author cover #{$author->id}");
            }));

        $this->info('Downloaded all author covers');
    }

    protected function getCoverImageResource(Author $author): mixed
    {
        $source = $this->scrap(Author::sourceUrl($author->id));

        $coverImageUrl = $this->getCoverImageUrlFromSource($source);

        return Http::withHeader('referer', 'https://tw.manhuagui.com/')
            ->get($coverImageUrl)
            ->resource();
    }

    protected function getCoverImageUrlFromSource(string $source): string
    {
        $crawler = new Crawler($source);

        return "https:{$crawler->filter('.pic')->attr('src')}";
    }
}
