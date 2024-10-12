<?php

namespace App\Jobs;

use App\Models\Chapter;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Communication\Message;
use HeadlessChromium\Page;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadChapter implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $uniqueFor = 60;

    public function __construct(
        public Chapter $chapter,
    ) {}

    public function handle(): void
    {
        $this->getAllPageImageUrls($this->chapter)->each(function (string $pageImageUrl, int $i) {
            Storage::disk('aliyun')->put(
                $this->chapter->pageImagePath($i + 1),
                $this->getPageImageResource($pageImageUrl),
            );
        });

        $this->chapter->update(['has_downloaded_pages' => true]);
    }

    protected function getPageImageResource(string $url): mixed
    {
        return Http::withHeader('referer', 'https://tw.manhuagui.com/')
            ->get($url)
            ->resource();
    }

    protected function getAllPageImageUrls(Chapter $chapter): Collection
    {
        $browserFactory = new BrowserFactory(
            app()->environment('production') ? 'google-chrome-stable' : null
        );

        $proxy = parse_url(config('app.proxy_url'));

        try {
            $browser = $browserFactory->createBrowser([
                'proxyServer' => "{$proxy['host']}:{$proxy['port']}",
                'userAgent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Safari/537.36',
                'headless' => true,
                'customFlags' => [
                    '--remote-allow-origins=*',
                    '--disable-site-isolation-trials',
                    '--disable-web-security',
                ],
            ]);

            $page = $browser->createPage();

            $this->applyProxyCredentials($page, $proxy['user'], $proxy['pass']);

            $page->navigate($chapter->sourceUrl());
            $page->waitUntilContainsElement('.mangaFile');

            $pageImageUrls = collect();

            while (true) {
                $pageImageUrl = $page->dom()->querySelector('.mangaFile')->getAttribute('src');

                $pageImageUrls->add($pageImageUrl);

                if (count($pageImageUrls) >= $chapter->pages) {
                    break;
                }

                $page->dom()->querySelector('#next')->click();

                $page->waitUntilContainsElement(".mangaFile:not([src='{$pageImageUrl}'])");
            }

            return $pageImageUrls;
        } finally {
            if (isset($browser)) {
                $browser->close();
            }
        }
    }

    protected function applyProxyCredentials(Page $page, string $username, string $password): void
    {
        $page->getSession()->sendMessageSync(new Message('Network.setRequestInterception', [
            'patterns' => [['urlPattern' => '*']],
        ]));

        $page->getSession()->on('method:Network.requestIntercepted', function (array $params) use ($page, $username, $password) {
            if (isset($params['authChallenge'])) {
                $page->getSession()->sendMessageSync(
                    new Message('Network.continueInterceptedRequest', [
                        'interceptionId' => $params['interceptionId'],
                        'authChallengeResponse' => [
                            'response' => 'ProvideCredentials',
                            'username' => $username,
                            'password' => $password,
                        ],
                    ])
                );
            } else {
                $page->getSession()->sendMessageSync(
                    new Message('Network.continueInterceptedRequest', [
                        'interceptionId' => $params['interceptionId'],
                    ])
                );
            }
        });
    }

    public function uniqueId(): string
    {
        return $this->chapter->id;
    }
}
