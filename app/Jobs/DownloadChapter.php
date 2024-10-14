<?php

namespace App\Jobs;

use App\Concerns\WithScraper;
use App\Exceptions\ChapterAlreadyLockedException;
use App\Exceptions\MissingPageException;
use App\Models\Chapter;
use App\Models\MissingPage;
use HeadlessChromium\BrowserFactory;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadChapter implements ShouldQueue, ShouldBeUnique
{
    use Queueable, WithScraper;

    public int $uniqueFor = 1800;

    public function __construct(
        public Chapter $chapter,
    ) {}

    public function handle(): void
    {
        try {
            if ($this->chapter->has_downloaded_pages) {
                throw new ChapterAlreadyLockedException("Chapter #{$this->chapter->id} already locked");
            }

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
                } catch (RequestException $e) {
                    if ($e->getCode() === 404 && Str::contains($e->response->body(), 'fetch_error')) {
                        MissingPage::updateOrCreate(['chapter_id' => $this->chapter->id, 'page' => $page]);
                        return;
                    }

                    throw $e;
                }

                Storage::put($imagePath, $resource);
            });

            $countExistingFiles = count(Storage::files($this->chapter->pageImageDirectory()));

            if ($countExistingFiles + $this->chapter->missingPages()->count() < $this->chapter->pages) {
                throw new MissingPageException("Missing page for chapter #{$this->chapter->id}");
            }

            $this->chapter->update(['has_downloaded_pages' => true]);
        } finally {
            $this->chapter->unlock();
        }
    }

    protected function getPageImageResource(string $url): mixed
    {
        return Http::proxy()
            ->retry(10, 1000)
            ->withHeader('referer', 'https://tw.manhuagui.com/')
            ->get($url)
            ->resource();
    }

    protected function getAllPageImageUrls(Chapter $chapter): Collection
    {
        $tempFile = sys_get_temp_dir() . '/' . Str::uuid() . '.html';
        $source = $this->scrap($chapter->sourceUrl());
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
                str_replace('.jpg.webp', '.jpg', "https://eu.hamreus.com{$data['path']}{$file}")
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

    public function uniqueId(): string
    {
        return $this->chapter->id;
    }
}
