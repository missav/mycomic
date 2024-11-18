<?php

namespace App\Jobs;

use App\Concerns\WithScraper;
use App\Exceptions\MissingChapterPageException;
use App\Models\Chapter;
use App\Models\MissingPage;
use HeadlessChromium\BrowserFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadChapter implements ShouldQueue
{
    use Queueable, WithScraper;

    public function __construct(
        public Chapter $chapter,
        public bool $force = false,
    ) {}

    public function middleware(): array
    {
        return [
            Skip::when($this->chapter->has_downloaded_pages && ! $this->force),
        ];
    }

    public function handle(): void
    {
        try {
            $existingFiles = Storage::files($this->chapter->pageImageDirectory());
            $existingFiles = collect($existingFiles)->map(fn(string $file) => "/{$file}");

            $this->getAllPageImageUrls($this->chapter)->each(function (string $pageImageUrl, int $i) use ($existingFiles) {
                $page = $i + 1;

                $imagePath = $this->chapter->pageImagePath($page);

                if ($existingFiles->contains($imagePath)) {
                    return;
                }

                try {
                    $resource = $this->getPageImageResource($pageImageUrl);
                } catch (RequestException) {
                    try {
                        $resource = $this->getPageImageResource(str_replace('//eu.', '//us.', $pageImageUrl));
                    } catch (RequestException $e) {
                        if ($e->getCode() === 404) {
                            MissingPage::updateOrCreate(['chapter_id' => $this->chapter->id, 'page' => $page]);
                            return;
                        }

                        throw $e;
                    }
                }

                Storage::put($imagePath, $resource);
            });

            $countExistingFiles = count(Storage::files($this->chapter->pageImageDirectory()));

            if ($countExistingFiles + $this->chapter->missingPages()->count() < $this->chapter->pages) {
                throw new MissingChapterPageException("Missing page for chapter #{$this->chapter->id}");
            }

            FetchChapterPageSizes::dispatchSync($this->chapter);

            $this->chapter->update(['has_downloaded_pages' => true]);
        } finally {
            $this->chapter->unlock();
        }
    }

    protected function getPageImageResource(string $url): mixed
    {
        return Http::proxy()
            ->retry(10, 1000)
            ->timeout(30)
            ->connectTimeout(30)
            ->withoutVerifying()
            ->withHeader('referer', 'https://tw.manhuagui.com/')
            ->get($url)
            ->resource();
    }

    protected function getAllPageImageUrls(Chapter $chapter): Collection
    {
        $source = $this->scrap($chapter->sourceUrl());
        if (Str::contains($source, '版權方')) {
            $source = $this->scrap($chapter->sourceUrl('www'));
        }

        $tempFile = sys_get_temp_dir() . '/' . Str::uuid() . '.html';
        file_put_contents($tempFile, $this->hackSource($source));

        $browserFactory = new BrowserFactory(
            app()->environment('production') ? 'google-chrome-stable' : null
        );

        try {
            $browser = $browserFactory->createBrowser([
                'userAgent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Safari/537.36',
                'headless' => true,
                'customFlags' => [
                    '--remote-allow-origins=*',
                    '--disable-site-isolation-trials',
                    '--disable-web-security',
                ],
            ]);

            $page = $browser->getPages()[0];

            $page->navigate("file://{$tempFile}");
            $page->waitUntilContainsElement('#ready');
            $data = $page->evaluate('window.data')->getReturnValue();

            return collect($data['files'])->map(fn (string $file) =>
                Str::replaceLast('.webp', '', "https://eu.hamreus.com{$data['path']}{$file}")
            );
        } finally {
            if (isset($browser)) {
                $browser->close();
            }

            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    protected function hackSource(string $source): string
    {
        return str($source)
            ->replace('src="//', 'src="https://')
            ->replace('<head>', <<<SCRIPT
            <head><script>
                SMH = {
                    set imgData(x) {
                        window.SMH = { ...window.SMH };
                        window.SMH.imgData = data => {
                            window.data = data;
                            document.body.innerHTML += '<div id="ready"></div>';
                        };
                    },
                };
            </script>
            SCRIPT);
    }
}
