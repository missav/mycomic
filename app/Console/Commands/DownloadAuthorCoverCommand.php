<?php

namespace App\Console\Commands;

use App\Models\Author;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class DownloadAuthorCoverCommand extends Command
{
    protected $signature = 'author:download-cover';

    protected $description = 'Download author cover command';

    public function handle(): void
    {
        Author::query()
            ->where('has_downloaded_cover', false)
            ->chunkById(100, fn (Collection $authors) => $authors->each(function (Author $author) {
                $this->info("Downloading author cover #{$author->id}");

                Storage::disk('aliyun')->put(
                    $author->coverImagePath(),
                    $this->getCoverImageResource($author),
                );

                $author->update(['has_downloaded_cover' => true]);

                $this->info("Downloaded author cover #{$author->id}");
            }));

        $this->info('Downloaded all author covers');
    }

    protected function getCoverImageResource(Author $author): mixed
    {
        $source = $this->getAuthorSource($author->id);

        $coverImageUrl = $this->getCoverImageUrlFromSource($source);

        return Http::withHeader('referer', 'https://tw.manhuagui.com/')
            ->get($coverImageUrl)
            ->resource();
    }

    protected function getAuthorSource(int $id): string
    {
        return Http::retry(5, 1000)
            ->connectTimeout(30)
            ->timeout(30)
            ->get(Author::sourceUrl($id))
            ->body();
    }

    protected function getCoverImageUrlFromSource(string $source): string
    {
        $crawler = new Crawler($source);

        return "https:{$crawler->filter('.pic')->attr('src')}";
    }
}
