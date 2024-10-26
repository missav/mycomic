<?php

namespace App;

use Illuminate\Support\Str;

class Seo
{
    protected static ?string $title = null;

    protected static ?string $description = null;

    protected static array $keywords = [];

    protected static array $authors = [];

    protected static ?string $image = null;

    protected static ?string $jsonLdScript = null;

    public static function title(?string $title = null): string
    {
        if ($title) {
            static::$title = $title;
        }

        $appName = config('app.name');

        return localized(static::$title ? static::$title . ' - ' . $appName : $appName);
    }

    public static function description(?string $description = null): string
    {
        if ($description) {
            static::$description = $description;
        }

        $fallbackDescription = 'MYCOMIC 我的漫畫擁有海量中文化漫畫，每日為你即時更新日本漫畫，韓國漫畫，國產漫畫與歐美漫畫。';

        return localized(Str::limit(static::$description ?? $fallbackDescription, 150));
    }

    public static function keywords(?array $keywords = null): string
    {
        if ($keywords) {
            static::$keywords = array_merge(static::$keywords, $keywords);
        } else if (! static::$keywords) {
            static::$keywords = [
                'MYCOMIC',
                '我的漫畫',
                '漫畫',
                '日本漫畫',
                '韓國漫畫',
                '國產漫畫',
                '歐美漫畫',
                '中文化漫畫',
                '中文化',
                '漢化組',
                '免費',
                '每日更新',
            ];
        }

        return collect(static::$keywords)->map(fn (string $keyword) => localized($keyword))->implode(', ');
    }

    public static function authors(?array $authors = null): string
    {
        if ($authors) {
            static::$authors = $authors;
        } else if (! static::$authors) {
            static::$authors = [
                config('app.name'),
            ];
        }

        return collect(static::$authors)->map(fn (string $author) => localized($author))->implode(', ');
    }

    public static function image(?string $image = null): string
    {
        if ($image) {
            static::$image = $image;
        } elseif (! static::$image) {
            static::$image = cdn('img/logo-square.png');
        }

        return static::$image;
    }

    public static function jsonLdScript(?string $jsonLdScript = null): string
    {
        if ($jsonLdScript) {
            static::$jsonLdScript = $jsonLdScript;
        }

        return static::$jsonLdScript ?? '';
    }

    public static function site(): string
    {
        return localized(config('app.name'));
    }

    public static function twitter(): string
    {
        return '@mycomiccom';
    }

    public static function gtmId(): string
    {
        return 'GTM-NBDC4TBL';
    }
}
